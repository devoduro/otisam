<?php

namespace App\Services\Notifications;

use App\Jobs\SendNotificationMessage;
use App\Models\NotificationMessage;
use App\Models\User;
use App\Services\Sms\SmsGateway;
use App\Services\Sms\SmsResult;
use App\Support\Phone;
use Illuminate\Database\QueryException;

/**
 * The one place messages leave the platform: one central notification service rather than SMS
 * calls in every controller.
 *
 * - queue(): logs the message and sends it from the queue after the current DB transaction
 *   commits. An idempotency key makes the same event send at most once per channel.
 * - smsNow(): sends immediately (one-time codes) and still logs it.
 *
 * Templates and per-user preferences come with the notifications module (P7). Callers already
 * pass an event key and a category, so those can be added without changing them.
 */
class Notifier
{
    public function __construct(private readonly SmsGateway $sms) {}

    /** Notify a user by SMS (if they have a phone) and email (if they have one). */
    public function toUser(User $user, string $eventKey, string $category, string $subject, string $body, ?string $idempotencyKey = null): void
    {
        if ($user->phone) {
            $this->queue(NotificationMessage::CHANNEL_SMS, $user->phone, $eventKey, $category, null, $body, $user->id, $idempotencyKey ? "{$idempotencyKey}:sms" : null);
        }
        if ($user->email) {
            $this->queue(NotificationMessage::CHANNEL_MAIL, $user->email, $eventKey, $category, $subject, $body, $user->id, $idempotencyKey ? "{$idempotencyKey}:mail" : null);
        }
    }

    public function queue(string $channel, string $recipient, string $eventKey, string $category, ?string $subject, string $body, ?int $userId = null, ?string $idempotencyKey = null): ?NotificationMessage
    {
        if ($idempotencyKey && NotificationMessage::where('idempotency_key', $idempotencyKey)->exists()) {
            return null;
        }

        try {
            $message = NotificationMessage::create([
                'idempotency_key' => $idempotencyKey,
                'event_key' => $eventKey,
                'category' => $category,
                'user_id' => $userId,
                'channel' => $channel,
                'recipient' => $channel === NotificationMessage::CHANNEL_SMS ? (string) Phone::clean($recipient) : $recipient,
                'subject' => $subject,
                'body' => $body,
                'status' => 'queued',
            ]);
        } catch (QueryException $e) {
            // Lost a race on the unique idempotency key: another request already queued it.
            if ($idempotencyKey && NotificationMessage::where('idempotency_key', $idempotencyKey)->exists()) {
                return null;
            }
            throw $e;
        }

        SendNotificationMessage::dispatch($message->id)->afterCommit();

        return $message;
    }

    /** Send an SMS right away. `storedBody` replaces the text kept in the log (for codes). */
    public function smsNow(string $phone, string $body, string $eventKey, string $category, ?int $userId = null, ?string $storedBody = null): SmsResult
    {
        $message = NotificationMessage::create([
            'event_key' => $eventKey,
            'category' => $category,
            'user_id' => $userId,
            'channel' => NotificationMessage::CHANNEL_SMS,
            'recipient' => (string) Phone::clean($phone),
            'body' => $storedBody ?? $body,
            'status' => 'queued',
        ]);

        $result = $this->sms->send($phone, $body);
        $this->recordSmsResult($message, $result);

        return $result;
    }

    public function recordSmsResult(NotificationMessage $message, SmsResult $result): void
    {
        $message->forceFill([
            'provider' => $this->sms->name(),
            'attempts' => $message->attempts + 1,
            'status' => $result->success ? 'sent' : 'failed',
            'provider_message_id' => $result->providerMessageId,
            'last_error' => $result->success ? null : mb_substr($result->code.': '.$result->message, 0, 490),
            'sent_at' => $result->success ? now() : null,
        ])->save();
    }
}
