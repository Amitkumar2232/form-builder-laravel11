<?php

use App\Http\Controllers\FormSubmissionExportController;
use App\Livewire\FormIndex;
use App\Livewire\FormSubmissions;
use App\Livewire\FormWizard;
use App\Livewire\PublicFormFill;
use App\Models\Form;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/forms');

Route::get('/forms', FormIndex::class)->name('forms.index');
Route::get('/forms/create', FormWizard::class)->name('forms.create');
Route::get('/forms/{form}/edit', FormWizard::class)->name('forms.edit');
Route::get('/forms/{form}', function (Form $form) {
    return redirect()->route('forms.edit', $form);
})->name('forms.show');
Route::get('/forms/{form}/submissions', FormSubmissions::class)->name('forms.submissions');
Route::get('/forms/{form}/submissions/export', [FormSubmissionExportController::class, 'csv'])->name('forms.submissions.export');

Route::get('/submit/{slug}', PublicFormFill::class)->name('forms.public');

Route::get('/embed/{slug}', function (string $slug) {
    $form = Form::where('slug', $slug)->where('status', 'published')->firstOrFail();

    return view('embed.form', compact('form'));
})->name('forms.embed');
