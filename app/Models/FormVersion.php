<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormVersion extends Model
{
    protected $fillable = [
        'form_id',
        'version_number',
        'schema',
        'settings',
        'change_note',
    ];

    protected function casts(): array
    {
        return [
            'schema' => 'array',
            'settings' => 'array',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
