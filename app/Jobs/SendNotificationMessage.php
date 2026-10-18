<?php

namespace App\Jobs;

use App\Mail\PlainNotificationMail;
use App\Models\NotificationMessage;
use App\Services\Notifications\Notifier;
use App\Services\Sms\SmsGateway;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendNotificationMessage implements ShouldQueue
{
    use Queueable;

    public int $tries = 4;

    /** @var array<int, int> seconds between retries of transient failures */
    public array $backoff = [60, 300, 900];

    public function __construct(public readonly int $messageId) {}

    public function handle(SmsGateway $sms, Notifier $notifier): void
    {
        $message = NotificationMessage::find($this->messageId);

        // Already sent (a retried job after a successful send) or deleted: nothing to do.
        if (! $message || in_array($message->status, ['sent', 'delivered', 'suppressed'], true)) {
            return;
        }

        if ($message->channel === NotificationMessage::CHANNEL_SMS) {
            $result = $sms->send($message->recipient, $message->body);
            $notifier->recordSmsResult($message, $result);

            if (! $result->success && $result->retryable && $this->attempts() < $this->tries) {
                $this->release($this->backoff[min($this->attempts() - 1, count($this->backoff) - 1)]);
            }

            return;
        }

        try {
            Mail::to($message->recipient)->send(new PlainNotificationMail((string) $message->subject, $message->body));
            $message->forceFill(['provider' => config('mail.default'), 'status' => 'sent', 'sent_at' => now(), 'attempts' => $message->attempts + 1, 'last_error' => null])->save();
        } catch (\Throwable $e) {
            $message->forceFill(['status' => 'failed', 'attempts' => $message->attempts + 1, 'last_error' => mb_substr($e->getMessage(), 0, 490)])->save();

            throw $e; // the queue retries with backoff
        }
    }
}
