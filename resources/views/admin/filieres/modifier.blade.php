<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Modifier la filière</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.filieres.update', $filiere) }}"
                class="rounded-lg bg-white p-6 shadow">
                @method('PUT')
                @include('admin.filieres.formulaire')
            </form>
        </div>
    </div>
</x-app-layout>