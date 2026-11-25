<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Mon historique de lecture</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            @include('lecteur.onglets')

            <div class="overflow-hidden rounded-lg bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Mémoire</th>
                            <th class="px-4 py-3">Filière</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($lectures as $lecture)
                            <tr>
                                <td class="px-4 py-3 text-gray-500">{{ $lecture->date_lecture->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('lecteur.detail', $lecture->memoire) }}"
                                        class="font-medium text-indigo-600 hover:underline">{{ $lecture->memoire->titre }}</a>
                                </td>
                                <td class="px-4 py-3">{{ $lecture->memoire->filiere?->code }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-gray-500">
                                    Vous n'avez encore lu aucun mémoire.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $lectures->links() }}</div>
        </div>
    </div>
</x-app-layout>