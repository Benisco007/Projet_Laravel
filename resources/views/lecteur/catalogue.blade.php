<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Catalogue des mémoires</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
            @include('admin.messages')
            @include('lecteur.onglets')

            <form method="GET" action="{{ route('lecteur.catalogue') }}"
                class="mb-6 flex flex-col gap-3 rounded-lg bg-white p-4 shadow sm:flex-row">
                <input type="text" name="recherche" value="{{ $recherche }}"
                    placeholder="Rechercher par titre, mot-clé ou résumé"
                    class="flex-1 rounded-md border-gray-300 text-sm shadow-sm">

                <select name="filiere" class="rounded-md border-gray-300 text-sm shadow-sm">
                    <option value="">Toutes les filières</option>
                    @foreach ($filieres as $filiere)
                        <option value="{{ $filiere->id }}" @selected($filiereId === $filiere->id)>
                            {{ $filiere->nom }}
                        </option>
                    @endforeach
                </select>

                <x-primary-button>Rechercher</x-primary-button>

                @if ($recherche !== '' || $filiereId)
                    <a href="{{ route('lecteur.catalogue') }}"
                        class="self-center text-sm text-gray-600 hover:underline">Effacer</a>
                @endif
            </form>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($memoires as $memoire)
                    @include('lecteur.carte', ['memoire' => $memoire])
                @empty
                    <p class="col-span-full rounded-lg bg-white p-6 text-center text-gray-500 shadow">
                        Aucun mémoire ne correspond à votre recherche.
                    </p>
                @endforelse
            </div>

            <div class="mt-6">{{ $memoires->links() }}</div>
        </div>
    </div>
</x-app-layout>