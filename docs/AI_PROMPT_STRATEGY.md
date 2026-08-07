# AI Prompt Strategy

## System Prompt

The system prompt defines a strict JSON output contract listing all allowed field types from `FieldTypeRegistry`. It instructs the model to:

- Output **only** valid JSON (enforced via OpenAI `response_format: json_object`)
- Use snake_case unique keys across the entire form
- Map unknown concepts to closest allowed types
- Include sensible placeholders, validations, and section groupings
- For edit mode, merge changes without removing unrelated fields

## Output Contract

The model must return a schema matching the structure validated by `FormSchemaValidator`:

- `version`, `title`, `description`, `sections[]`, `settings`
- Each field: `id`, `type`, `key`, `label`, `placeholder`, `help_text`, `required`, `options`, `validation`, `conditions`

## Hallucinated Field Types

Unknown types are normalized in two layers:

1. **Alias map** in `FieldTypeRegistry::normalizeType()` (e.g. `select` → `dropdown`, `upload` → `file`)
2. **Fallback** to `text` if type is not in the allowed list

After normalization, `FormSchemaValidator::repairPartial()` fills missing IDs, keys, and validation defaults.

## Retries & Fallbacks

`FormAiGenerator` retries up to 3 times:

1. Initial generation
2. Retry with explicit "previous response was invalid" message
3. Final retry, then `repairPartial()` if validation still fails

If all attempts fail, the job is marked `failed` and nothing is persisted.

**No API key fallback**: When `OPENAI_API_KEY` is unset, a deterministic mock generator creates a sensible form based on prompt keywords (e.g. "resume" adds a file field). This allows full local testing without API costs.

## Edit Mode

Edit prompts include the full existing schema JSON plus the user's instruction. The system prompt tells the model to merge, not replace wholesale.

Examples:
- "add an emergency contact section" → append new section
- "make phone required" → update existing phone field
- "translate labels to Hindi" → update labels in place

## Logging

Every job records to `ai_generation_logs`:

- `model`, `prompt_tokens`, `completion_tokens`, `latency_ms`, `retry_count`
- Structured log entry on completion via `Log::info()`

## Queue Architecture

Generation runs via `GenerateFormFromPromptJob` on the default queue. The UI polls `pollAiStatus()` every 2 seconds — never blocking the HTTP request on LLM latency.

## Import Hybrid Approach

Word/Excel import uses **deterministic parsing first**:

- Word: PhpWord element traversal, heading styles → sections, regex for options/required/type hints
- Excel: header-row or field-definition layout detection

AI refinement during import is optional and disabled by default in `DocumentImportService` — the preview/mapping screen lets users fix misdetected types before commit. This avoids non-deterministic import results and reduces API cost.

## Recommended Production Settings

```env
OPENAI_API_KEY=sk-...
OPENAI_MODEL=gpt-4o-mini
QUEUE_CONNECTION=redis  # or database
```

Use `gpt-4o` for complex multi-section forms; `gpt-4o-mini` for cost-efficient generation.
