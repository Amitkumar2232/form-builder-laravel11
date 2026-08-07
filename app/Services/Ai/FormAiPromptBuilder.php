<?php

namespace App\Services\Ai;

use App\Services\FieldTypeRegistry;
use App\Services\FormSchemaValidator;

class FormAiPromptBuilder
{
    public function systemPrompt(): string
    {
        $types = implode(', ', FieldTypeRegistry::ALL_TYPES);

        return <<<PROMPT
You are a form schema generator. Output ONLY valid JSON — no markdown, no explanation.

Schema contract:
{
  "version": 1,
  "title": "string",
  "description": "string",
  "sections": [
    {
      "id": "uuid-string",
      "title": "Section title",
      "fields": [
        {
          "id": "uuid-string",
          "type": "one of: {$types}",
          "key": "snake_case_unique_key",
          "label": "Human label",
          "placeholder": "optional",
          "help_text": "optional",
          "default": null,
          "required": boolean,
          "options": [{"label":"...", "value":"..."}],
          "validation": {
            "min": null, "max": null,
            "min_length": null, "max_length": null,
            "regex": null,
            "file_types": [], "max_file_size_kb": null
          },
          "conditions": null or {"show_when": {"field":"key", "operator":"equals|not_equals|contains|not_empty|empty", "value":"..."}}
        }
      ]
    }
  ],
  "settings": {
    "submit_button_text": "Submit",
    "success_message": "Thank you!",
    "allow_multiple_submissions": true
  }
}

Rules:
- Use sensible field types; never invent types outside the allowed list.
- Map unknown concepts to closest allowed type (e.g. "URL" → text with validation hint in help_text).
- Include placeholders and validations where appropriate.
- Group related fields into sections.
- For edit mode, merge changes into the existing schema without removing unrelated fields unless asked.
- Keys must be unique snake_case across the entire form.
PROMPT;
    }

    public function createPrompt(string $userPrompt): string
    {
        return "Create a complete form schema for: {$userPrompt}";
    }

    public function editPrompt(string $userPrompt, array $existingSchema): string
    {
        $json = json_encode($existingSchema, JSON_UNESCAPED_UNICODE);

        return "Edit this existing form schema according to the instruction.\nInstruction: {$userPrompt}\n\nExisting schema:\n{$json}";
    }
}
