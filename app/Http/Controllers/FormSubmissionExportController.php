<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FormSubmissionExportController extends Controller
{
    public function csv(Form $form, Request $request): StreamedResponse
    {
        $query = $form->submissions()->orderByDesc('submitted_at');

        if ($search = $request->get('search')) {
            $query->where('data', 'like', '%'.$search.'%');
        }

        $submissions = $query->get();
        $keys = $this->collectFieldKeys($form);

        return response()->streamDownload(function () use ($submissions, $keys, $form) {
            $out = fopen('php://output', 'w');
            fputcsv($out, array_merge(['id', 'submitted_at'], $keys));

            foreach ($submissions as $submission) {
                $row = [
                    $submission->id,
                    $submission->submitted_at?->toDateTimeString(),
                ];
                foreach ($keys as $key) {
                    $val = $submission->data[$key] ?? '';
                    $row[] = is_array($val) ? implode('; ', $val) : $val;
                }
                fputcsv($out, $row);
            }

            fclose($out);
        }, Str::slug($form->title).'-submissions.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    protected function collectFieldKeys(Form $form): array
    {
        $keys = [];
        foreach ($form->schema['sections'] ?? [] as $section) {
            foreach ($section['fields'] ?? [] as $field) {
                if (! empty($field['key'])) {
                    $keys[] = $field['key'];
                }
            }
        }

        return array_unique($keys);
    }
}
