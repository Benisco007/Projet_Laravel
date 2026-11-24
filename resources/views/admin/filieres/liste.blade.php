<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Filières</h2>
            <a href="{{ route('admin.filieres.create') }}"
                class="rounded-md bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-700">
                Nouvelle filière
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            @include('admin.messages')

            <div class="overflow-hidden rounded-lg bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Code</th>
                            <th class="px-4 py-3">Nom</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($filieres as $filiere)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $filiere->code }}</td>
                                <td class="px-4 py-3">{{ $filiere->nom }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.filieres.edit', $filiere) }}"
                                            class="text-indigo-600 hover:underline">Modifier</a>
                                        <form method="POST" action="{{ route('admin.filieres.destroy', $filiere) }}"
                                            onsubmit="return confirm('Supprimer cette filière ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-gray-500">Aucune filière.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>