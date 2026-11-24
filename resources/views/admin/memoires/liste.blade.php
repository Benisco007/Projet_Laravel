<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Validation des mémoires</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
            @include('admin.messages')

            <div class="mb-4 flex gap-2 text-sm">
                @foreach (['en_attente' => 'En attente', 'valide' => 'Validés', 'rejete' => 'Rejetés'] as $valeur => $libelle)
                    <a href="{{ route('admin.memoires.index', ['statut' => $valeur]) }}"
                        class="rounded-md px-3 py-1.5 {{ $statut === $valeur ? 'bg-gray-800 text-white' : 'bg-white text-gray-700 shadow hover:bg-gray-50' }}">
                        {{ $libelle }}
                    </a>
                @endforeach
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Titre</th>
                            <th class="px-4 py-3">Auteur</th>
                            <th class="px-4 py-3">Filière</th>
                            <th class="px-4 py-3">Année</th>
                            <th class="px-4 py-3">Similarité</th>
                            @if ($statut === 'en_attente')
                                <th class="px-4 py-3 text-right">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($memoires as $memoire)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $memoire->titre }}</td>
                                <td class="px-4 py-3">{{ $memoire->auteur }}</td>
                                <td class="px-4 py-3">{{ $memoire->filiere->code }}</td>
                                <td class="px-4 py-3">{{ $memoire->annee }}</td>
                                <td class="px-4 py-3">
                                    @if ($memoire->taux_similarite === null)
                                        <span class="text-gray-400">Non calculée</span>
                                    @elseif ($memoire->estPlagiatSignale())
                                        <span class="rounded bg-red-100 px-2 py-0.5 text-red-800">
                                            {{ $memoire->taux_similarite }} % : plagiat signalé
                                        </span>
                                    @else
                                        {{ $memoire->taux_similarite }} %
                                    @endif
                                </td>
                                @if ($statut === 'en_attente')
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-3">
                                            <form method="POST" action="{{ route('admin.memoires.valider', $memoire) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="text-green-700 hover:underline">Valider</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.memoires.rejeter', $memoire) }}"
                                                onsubmit="return confirm('Rejeter ce mémoire ?')">
                                                @csrf
                                                @method('PATCH')
                                                <button class="text-red-600 hover:underline">Rejeter</button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">Aucun mémoire.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $memoires->links() }}</div>
        </div>
    </div>
</x-app-layout>