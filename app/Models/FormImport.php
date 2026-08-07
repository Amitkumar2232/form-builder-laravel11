<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormImport extends Model
{
    protected $fillable = [
        'import_uuid',
        'form_id',
        'original_filename',
        'file_type',
        'stored_path',
        'status',
        'parsed_schema',
        'mapping_overrides',
        'warnings',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'parsed_schema' => 'array',
            'mapping_overrides' => 'array',
            'warnings' => 'array',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
