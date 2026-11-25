<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">{{ $memoire->titre }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            @include('admin.messages')

            <a href="{{ route('lecteur.catalogue') }}" class="mb-4 inline-block text-sm text-gray-600 hover:underline">
                ← Retour au catalogue
            </a>

            <div class="rounded-lg bg-white p-6 shadow">
                <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-gray-500">Auteur</dt>
                        <dd class="font-medium">{{ $memoire->etudiant?->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Filière</dt>
                        <dd class="font-medium">{{ $memoire->filiere?->nom ?? 'Non renseignée' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Année académique</dt>
                        <dd class="font-medium">{{ $memoire->annee_academique }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Encadreur</dt>
                        <dd class="font-medium">{{ $memoire->encadreur?->name ?? 'Non renseigné' }}</dd>
                    </div>
                </dl>

                @if ($memoire->mots_cles)
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach (explode(',', $memoire->mots_cles) as $mot)
                            <span class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ trim($mot) }}</span>
                        @endforeach
                    </div>
                @endif

                <h3 class="mt-6 font-semibold">Résumé</h3>
                <p class="mt-2 whitespace-pre-line text-sm text-gray-700">{{ $memoire->resume }}</p>

                @if ($limitePages)
                    <div class="mt-6 rounded-md bg-yellow-50 p-3 text-sm text-yellow-800">
                        En tant que visiteur externe, vous pouvez lire les {{ $limitePages }} premières pages.
                        Pour obtenir le mémoire complet, faites une demande.
                    </div>
                @elseif ($accesComplet)
                    <div class="mt-6 rounded-md bg-green-50 p-3 text-sm text-green-800">
                        Vous avez acheté la version numérique de ce mémoire : vous pouvez le lire en entier.
                    </div>
                @endif

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <a href="{{ route('lecteur.lire', $memoire) }}"
                        class="rounded-md bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-700">
                        {{ $limitePages ? 'Lire l\'aperçu' : 'Lire le mémoire' }}
                    </a>

                    <form method="POST" action="{{ route('lecteur.favori', $memoire) }}">
                        @csrf
                        <button class="rounded-md bg-white px-4 py-2 text-sm text-gray-700 shadow hover:bg-gray-50">
                            {{ $estFavori ? '★ Retirer des favoris' : '☆ Ajouter aux favoris' }}
                        </button>
                    </form>

                    @if ($limitePages)
                        <a href="{{ route('lecteur.obtenir', $memoire) }}"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-500">
                            Obtenir le mémoire complet
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>