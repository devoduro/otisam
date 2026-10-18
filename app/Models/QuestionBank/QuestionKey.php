<?php

namespace App\Models\QuestionBank;

use Illuminate\Database\Eloquent\Model;

/** Answer key for one revision. Loaded only by the marking service and staff allowed to see keys. */
class QuestionKey extends Model
{
    public const UPDATED_AT = null;

    public $incrementing = false;

    protected $primaryKey = 'question_revision_id';

    protected $guarded = ['*'];

    protected function casts(): array
    {
        return ['key' => 'array'];
    }
}
