<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Demandes d'obtention de mémoires</h2>
            <a href="{{ route('admin.tableau') }}" class="text-sm text-gray-600 hover:underline">
                ← Administration
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
            @include('admin.messages')

            <div class="overflow-hidden rounded-lg bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Demandeur</th>
                            <th class="px-4 py-3">Mémoire</th>
                            <th class="px-4 py-3">Souhaite</th>
                            <th class="px-4 py-3">Montant</th>
                            <th class="px-4 py-3">Statut</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($demandes as $demande)
                            <tr>
                                <td class="px-4 py-3 text-gray-500">{{ $demande->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $demande->user->name }}</div>
                                    <div class="text-gray-500">{{ $demande->user->email }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $demande->memoire->titre }}</td>
                                <td class="px-4 py-3">{{ $demande->libelleMode() }}</td>
                                <td class="px-4 py-3">
                                    <div>{{ \App\Models\DemandeAchat::prixLibelle($demande->montant) }}</div>
                                    @if ($demande->reference)
                                        <div class="font-mono text-xs text-gray-500">{{ $demande->reference }}</div>
                                    @endif
                                    @if ($demande->paye_le)
                                        <div class="text-xs text-green-700">Payé (simulation)</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($demande->statut === 'nouvelle')
                                        <span class="rounded bg-yellow-100 px-2 py-0.5 text-yellow-800">Nouvelle</span>
                                    @else
                                        <span class="rounded bg-green-100 px-2 py-0.5 text-green-800">Traitée</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if ($demande->statut === 'nouvelle')
                                        <form method="POST" action="{{ route('admin.demandes.traiter', $demande) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="text-green-700 hover:underline">Marquer traitée</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">Aucune demande.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $demandes->links() }}</div>
        </div>
    </div>
</x-app-layout>