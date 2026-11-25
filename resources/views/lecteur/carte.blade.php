<a href="{{ route('lecteur.detail', $memoire) }}"
    class="block rounded-lg bg-white p-5 shadow hover:shadow-md">
    <span class="rounded bg-indigo-50 px-2 py-0.5 text-xs text-indigo-700">
        {{ $memoire->filiere?->code ?? 'Sans filière' }}
    </span>
    <h3 class="mt-3 font-semibold text-gray-900">{{ $memoire->titre }}</h3>
    <p class="mt-1 text-sm text-gray-500">
        {{ $memoire->etudiant?->name }} · {{ $memoire->annee_academique }}
    </p>
    <p class="mt-3 text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($memoire->resume, 120) }}</p>
</a>