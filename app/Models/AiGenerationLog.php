<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiGenerationLog extends Model
{
    protected $fillable = [
        'job_uuid',
        'form_id',
        'mode',
        'prompt',
        'status',
        'result_schema',
        'error_message',
        'model',
        'prompt_tokens',
        'completion_tokens',
        'latency_ms',
        'retry_count',
    ];

    protected function casts(): array
    {
        return [
            'result_schema' => 'array',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
