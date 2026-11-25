<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Obtenir le mémoire complet</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            @include('admin.messages')

            <a href="{{ route('lecteur.detail', $memoire) }}" class="mb-4 inline-block text-sm text-gray-600 hover:underline">
                ← Retour à la fiche du mémoire
            </a>

            <form method="GET" action="{{ route('lecteur.paiement', $memoire) }}"
                class="rounded-lg bg-white p-6 shadow">
                <h3 class="font-semibold text-gray-900">{{ $memoire->titre }}</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Choisissez comment vous souhaitez obtenir ce mémoire. Le paiement est une simulation :
                    aucun argent réel n'est débité.
                </p>

                <div class="mt-5 space-y-3">
                    @foreach ($modes as $cle => $libelle)
                        <label class="flex items-center justify-between gap-3 rounded-md border border-gray-200 p-3 text-sm hover:bg-gray-50">
                            <span class="flex items-center gap-3">
                                <input type="radio" name="mode" value="{{ $cle }}" required>
                                <span>{{ $libelle }}</span>
                            </span>
                            <span class="font-medium text-gray-700">
                                {{ \App\Models\DemandeAchat::prixLibelle($prix[$cle]) }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <div class="mt-6 flex items-center gap-4">
                    <x-primary-button>Continuer</x-primary-button>
                    <a href="{{ route('lecteur.detail', $memoire) }}" class="text-sm text-gray-600 hover:underline">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>