<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Mes favoris</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
            @include('admin.messages')
            @include('lecteur.onglets')

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($memoires as $memoire)
                    @include('lecteur.carte', ['memoire' => $memoire])
                @empty
                    <p class="col-span-full rounded-lg bg-white p-6 text-center text-gray-500 shadow">
                        Vous n'avez pas encore de favori. Ouvrez la fiche d'un mémoire pour l'ajouter.
                    </p>
                @endforelse
            </div>

            <div class="mt-6">{{ $memoires->links() }}</div>
        </div>
    </div>
</x-app-layout>