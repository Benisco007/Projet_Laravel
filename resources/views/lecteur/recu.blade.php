<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Reçu</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow">
                <div class="mb-4 rounded-md bg-green-50 p-3 text-sm text-green-800">
                    @if ($demande->paye_le)
                        Paiement simulé validé. Votre demande a été transmise à la bibliothèque.
                    @else
                        Votre demande a été transmise à la bibliothèque.
                    @endif
                </div>

                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Référence</dt>
                        <dd class="font-mono font-medium">{{ $demande->reference }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Mémoire</dt>
                        <dd class="text-right font-medium">{{ $memoire->titre }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Option</dt>
                        <dd class="font-medium">{{ $demande->libelleMode() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Montant</dt>
                        <dd class="font-medium">{{ \App\Models\DemandeAchat::prixLibelle($demande->montant) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Date</dt>
                        <dd class="font-medium">{{ $demande->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>

                <p class="mt-4 text-xs text-gray-500">Reçu de simulation : aucun paiement réel n'a eu lieu.</p>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    @if ($demande->mode === 'en_ligne' && $demande->paye_le)
                        <a href="{{ route('lecteur.lire', $memoire) }}"
                            class="rounded-md bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-700">
                            Lire le mémoire complet
                        </a>
                    @endif
                    <a href="{{ route('lecteur.detail', $memoire) }}" class="text-sm text-gray-600 hover:underline">
                        Retour à la fiche du mémoire
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>