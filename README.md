# AI-Powered Form Builder

A Laravel 11 + Livewire 4 form builder with manual creation, AI generation, Word/Excel import, public fill URLs, and submission management.

## Requirements

- PHP 8.2+
- MySQL 8+ (recommended) or SQLite for local dev
- Composer
- Node.js (optional, for Vite assets)
- Queue worker for AI/import jobs (`php artisan queue:work`)

## Quick Start

```bash
composer install
cp .env.example .env
php artisan key:generate

# Configure MySQL in .env:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_DATABASE=form_builder
# DB_USERNAME=root
# DB_PASSWORD=

php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan forms:generate-samples

# Optional: set OPENAI_API_KEY in .env for real AI generation
# Without it, a deterministic mock generator is used

php artisan serve
php artisan queue:work   # separate terminal
```

Visit `http://localhost:8000/forms`

## Features

### Part A — Core Form Builder
- 4-step wizard: Details → Builder → Settings → Finish
- 15 field types: text, textarea, number, email, phone, date, dropdown, radio, checkbox, file, rating, hidden, heading, description, newline
- Drag-and-drop reorder (SortableJS), click-to-add, duplicate, inline edit, delete
- Per-field config: label, key, placeholder, help text, default, required, options, validation rules
- JSON schema as single source of truth with two-way sync editor
- Server-side validation derived from schema on public submit
- Submissions list with pagination, search, CSV export

### Part B — AI Form Generation
- Natural-language prompt → complete editable form
- Edit existing forms via AI ("add emergency contact", "make phone required")
- Queued job with polling status UI
- Logs model, tokens, latency to `ai_generation_logs`
- Validates/repairs JSON; never persists broken schema
- Mock fallback when `OPENAI_API_KEY` is unset

### Part C — Word & Excel Import
- **Word (.docx)**: headings → sections, numbered questions → fields, bracket lists → options
- **Excel (.xlsx)**: two layouts supported:
  1. Header row (row 1 = labels, optional row 2 = types)
  2. Field definition sheet (columns: label, type, required, options, placeholder)
- Deterministic parsing first; preview before commit
- Queued processing for large files
- Sample files in `samples/` (run `php artisan forms:generate-samples`)

### Part D — Differentiators

#### 1. Form Versioning & Rollback
- **Problem**: Accidental schema changes lose previous work
- **Implementation**: Every save snapshots to `form_versions`; rollback from Settings step
- **Trade-offs**: Storage grows with edits; no diff view yet
- **More time**: Visual diff, named checkpoints, auto-prune old versions

#### 2. Conditional Logic
- **Problem**: Forms need fields that appear based on prior answers
- **Implementation**: `conditions.show_when` on fields; evaluated server-side on submit
- **Trade-offs**: UI is basic (field key + operator); no nested AND/OR groups
- **More time**: Visual rule builder, client-side live show/hide

#### 3. Template Library
- **Problem**: Users rebuild common forms from scratch
- **Implementation**: Seeded templates (Contact, Job Application, Event Registration); start new form from template
- **Trade-offs**: Templates are admin-seeded, not user-created yet
- **More time**: User-save-as-template, categories, preview thumbnails

#### Bonus: Embeddable Widget + Rate Limiting
- `/embed/{slug}` iframe embed for published forms
- Rate limit: 5 submissions/minute per IP per form

## MySQL Indexes

| Table | Index | Purpose |
|-------|-------|---------|
| `forms` | `uuid` UNIQUE | Public API lookups |
| `forms` | `slug` UNIQUE | Public fill URL routing |
| `forms` | `status` | Filter published/draft lists |
| `forms` | `(status, created_at)` | Dashboard listing at scale |
| `forms` | `title` | Search |
| `forms` | `is_template` | Template library queries |
| `form_submissions` | `(form_id, submitted_at)` | Paginated submission lists |
| `form_submissions` | `(form_id, created_at)` | Export and analytics |
| `form_versions` | `(form_id, version_number)` UNIQUE | Rollback lookups |
| `ai_generation_logs` | `(status, created_at)` | Job monitoring |
| `form_imports` | `(status, created_at)` | Import queue monitoring |

## AI Prompt Strategy

See [docs/AI_PROMPT_STRATEGY.md](docs/AI_PROMPT_STRATEGY.md)

## JSON Schema Contract

```json
{
  "version": 1,
  "title": "Form Title",
  "sections": [{
    "id": "uuid",
    "title": "Section",
    "fields": [{
      "id": "uuid",
      "type": "text",
      "key": "field_key",
      "label": "Label",
      "required": false,
      "validation": { "min_length": null, "max_length": null }
    }]
  }],
  "settings": {
    "submit_button_text": "Submit",
    "success_message": "Thank you!",
    "allow_multiple_submissions": true
  }
}
```

## Routes

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/forms` | List forms & templates |
| GET | `/forms/create` | New form wizard |
| GET | `/forms/{form}/edit` | Edit form |
| GET | `/submit/{slug}` | Public fill URL |
| GET | `/embed/{slug}` | Embeddable iframe |
| GET | `/forms/{form}/submissions` | View submissions |
| GET | `/forms/{form}/submissions/export` | CSV export |

## Testing Import Samples

```bash
php artisan forms:generate-samples
# Upload samples/sample-survey.docx or samples/sample-field-definition.xlsx
# via the import panel on Step 1 (Details)
```

## License

MIT
