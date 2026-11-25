<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Paiement</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <a href="{{ route('lecteur.obtenir', $memoire) }}" class="mb-4 inline-block text-sm text-gray-600 hover:underline">
                ← Changer d'option
            </a>

            <div class="mb-4 rounded-md bg-yellow-50 p-3 text-sm text-yellow-800">
                <strong>Simulation :</strong> aucun argent réel n'est débité. Entrez un numéro inventé,
                il n'est pas enregistré.
            </div>

            <form method="POST" action="{{ route('lecteur.payer', $memoire) }}"
                class="rounded-lg bg-white p-6 shadow">
                @csrf
                <input type="hidden" name="mode" value="{{ $mode }}">

                <h3 class="font-semibold text-gray-900">{{ $memoire->titre }}</h3>
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Option</dt>
                        <dd class="font-medium">{{ $libelleMode }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Montant</dt>
                        <dd class="font-medium">{{ \App\Models\DemandeAchat::prixLibelle($montant) }}</dd>
                    </div>
                </dl>

                @if ($montant > 0)
                    <div class="mt-6">
                        <x-input-label value="Moyen de paiement" />
                        <div class="mt-2 space-y-2">
                            @foreach ($moyens as $cle => $libelle)
                                <label class="flex items-center gap-3 rounded-md border border-gray-200 p-3 text-sm hover:bg-gray-50">
                                    <input type="radio" name="moyen" value="{{ $cle }}" @checked(old('moyen') === $cle) required>
                                    <span>{{ $libelle }}</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('moyen')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="numero" value="Numéro de téléphone (fictif)" />
                        <x-text-input id="numero" name="numero" type="text" class="mt-1 block w-full"
                            :value="old('numero')" placeholder="Votre numéro" required />
                        <x-input-error :messages="$errors->get('numero')" class="mt-2" />
                    </div>
                @else
                    <p class="mt-6 text-sm text-gray-600">
                        Cette option est gratuite : aucun paiement n'est nécessaire.
                        La bibliothèque recevra votre demande.
                    </p>
                @endif

                <x-input-error :messages="$errors->get('mode')" class="mt-2" />

                <div class="mt-6 flex items-center gap-4">
                    <x-primary-button>
                        {{ $montant > 0 ? 'Payer ' . \App\Models\DemandeAchat::prixLibelle($montant) . ' (simulation)' : 'Confirmer la demande' }}
                    </x-primary-button>
                    <a href="{{ route('lecteur.detail', $memoire) }}" class="text-sm text-gray-600 hover:underline">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>