<?php

namespace App\Services\Ai;

use App\Models\AiGenerationLog;
use App\Services\FormSchemaValidator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FormAiGenerator
{
    public function __construct(
        protected FormAiPromptBuilder $promptBuilder,
        protected FormSchemaValidator $validator,
    ) {}

    public function generate(AiGenerationLog $log): array
    {
        $log->update(['status' => 'processing']);

        $start = microtime(true);
        $maxRetries = 3;
        $lastError = null;

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            try {
                $response = $this->callLlm($log, $attempt);
                $schema = $this->extractAndValidate($response['content'], $log);

                $log->update([
                    'status' => 'completed',
                    'result_schema' => $schema,
                    'model' => $response['model'],
                    'prompt_tokens' => $response['prompt_tokens'],
                    'completion_tokens' => $response['completion_tokens'],
                    'latency_ms' => (int) ((microtime(true) - $start) * 1000),
                    'retry_count' => $attempt,
                ]);

                Log::info('AI form generation completed', [
                    'job_uuid' => $log->job_uuid,
                    'model' => $response['model'],
                    'tokens' => ($response['prompt_tokens'] ?? 0) + ($response['completion_tokens'] ?? 0),
                    'latency_ms' => $log->latency_ms,
                ]);

                return $schema;
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                Log::warning('AI form generation attempt failed', [
                    'job_uuid' => $log->job_uuid,
                    'attempt' => $attempt + 1,
                    'error' => $lastError,
                ]);
            }
        }

        $log->update([
            'status' => 'failed',
            'error_message' => $lastError,
            'latency_ms' => (int) ((microtime(true) - $start) * 1000),
            'retry_count' => $maxRetries,
        ]);

        throw new \RuntimeException($lastError ?? 'AI generation failed');
    }

    protected function callLlm(AiGenerationLog $log, int $attempt): array
    {
        $apiKey = config('services.openai.api_key');
        $model = config('services.openai.model', 'gpt-4o-mini');

        if (empty($apiKey)) {
            return $this->mockResponse($log);
        }

        $messages = [
            ['role' => 'system', 'content' => $this->promptBuilder->systemPrompt()],
        ];

        if ($log->mode === 'edit' && $log->form?->schema) {
            $messages[] = [
                'role' => 'user',
                'content' => $this->promptBuilder->editPrompt($log->prompt, $log->form->schema),
            ];
        } else {
            $messages[] = [
                'role' => 'user',
                'content' => $this->promptBuilder->createPrompt($log->prompt),
            ];
        }

        if ($attempt > 0) {
            $messages[] = [
                'role' => 'user',
                'content' => 'Your previous response was invalid JSON or failed validation. Return ONLY corrected valid JSON matching the schema contract.',
            ];
        }

        $response = Http::withToken($apiKey)
            ->timeout(120)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.3,
                'response_format' => ['type' => 'json_object'],
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('OpenAI API error: '.$response->body());
        }

        $body = $response->json();

        return [
            'content' => $body['choices'][0]['message']['content'] ?? '',
            'model' => $body['model'] ?? $model,
            'prompt_tokens' => $body['usage']['prompt_tokens'] ?? null,
            'completion_tokens' => $body['usage']['completion_tokens'] ?? null,
        ];
    }

    protected function mockResponse(AiGenerationLog $log): array
    {
        $schema = [
            'version' => 1,
            'title' => 'Generated: '.Str::limit($log->prompt, 50),
            'description' => 'Auto-generated from prompt (mock — set OPENAI_API_KEY for real AI)',
            'sections' => [
                [
                    'id' => (string) Str::uuid(),
                    'title' => 'Details',
                    'fields' => [
                        array_merge(\App\Services\FieldTypeRegistry::defaultField('text'), [
                            'key' => 'full_name',
                            'label' => 'Full Name',
                            'placeholder' => 'Enter your full name',
                            'required' => true,
                            'validation' => ['min_length' => 2, 'max_length' => 100],
                        ]),
                        array_merge(\App\Services\FieldTypeRegistry::defaultField('email'), [
                            'key' => 'email',
                            'label' => 'Email Address',
                            'required' => true,
                        ]),
                        array_merge(\App\Services\FieldTypeRegistry::defaultField('textarea'), [
                            'key' => 'details',
                            'label' => 'Additional Details',
                            'placeholder' => 'Tell us more...',
                        ]),
                    ],
                ],
            ],
            'settings' => [
                'submit_button_text' => 'Submit',
                'success_message' => 'Thank you!',
                'allow_multiple_submissions' => true,
            ],
        ];

        if (Str::contains(strtolower($log->prompt), ['resume', 'upload', 'file'])) {
            $schema['sections'][0]['fields'][] = array_merge(
                \App\Services\FieldTypeRegistry::defaultField('file'),
                [
                    'key' => 'resume',
                    'label' => 'Resume Upload',
                    'required' => true,
                    'validation' => ['file_types' => ['pdf', 'doc', 'docx'], 'max_file_size_kb' => 5120],
                ]
            );
        }

        return [
            'content' => json_encode($schema),
            'model' => 'mock-local',
            'prompt_tokens' => 0,
            'completion_tokens' => 0,
        ];
    }

    protected function extractAndValidate(string $content, AiGenerationLog $log): array
    {
        $content = trim($content);
        $content = preg_replace('/^```json\s*|\s*```$/', '', $content);

        $decoded = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON from LLM: '.json_last_error_msg());
        }

        try {
            return $this->validator->validate($decoded);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validator->repairPartial($decoded);
        }
    }
}
