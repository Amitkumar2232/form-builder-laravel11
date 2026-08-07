<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Form extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'slug',
        'title',
        'description',
        'schema',
        'settings',
        'status',
        'version',
        'is_template',
        'template_category',
    ];

    protected function casts(): array
    {
        return [
            'schema' => 'array',
            'settings' => 'array',
            'is_template' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Form $form) {
            $form->uuid ??= (string) Str::uuid();
            $form->slug ??= Str::slug($form->title).'-'.Str::random(6);
        });
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(FormVersion::class)->orderByDesc('version_number');
    }

    public function aiLogs(): HasMany
    {
        return $this->hasMany(AiGenerationLog::class);
    }

    public function imports(): HasMany
    {
        return $this->hasMany(FormImport::class);
    }

    public function publicUrl(): string
    {
        return url('/submit/'.$this->slug);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
