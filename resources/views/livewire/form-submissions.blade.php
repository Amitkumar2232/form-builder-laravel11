<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('forms.index') }}" class="text-sm text-gray-500 hover:text-blue-600">← Back to forms</a>
            <h1 class="text-2xl font-bold mt-1">{{ $form->title }} — Submissions</h1>
        </div>
        <a href="{{ route('forms.submissions.export', $form) }}?search={{ $search }}"
            class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">Export CSV</a>
    </div>

    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search submissions..."
        class="w-full max-w-md border rounded-lg px-4 py-2 mb-4">

    <div class="bg-white border rounded-xl overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3">ID</th>
                    <th class="text-left px-4 py-3">Submitted</th>
                    <th class="text-left px-4 py-3">IP</th>
                    <th class="text-left px-4 py-3">Data Preview</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $submission)
                    <tr class="border-b">
                        <td class="px-4 py-3">#{{ $submission->id }}</td>
                        <td class="px-4 py-3">{{ $submission->submitted_at?->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $submission->ip_address }}</td>
                        <td class="px-4 py-3">
                            <code class="text-xs bg-gray-50 px-2 py-1 rounded">{{ \Illuminate\Support\Str::limit(json_encode($submission->data), 80) }}</code>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No submissions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $submissions->links() }}</div>
</div>
