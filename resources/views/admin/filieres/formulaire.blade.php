@csrf

<div>
    <x-input-label for="nom" value="Nom" />
    <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full"
        :value="old('nom', $filiere->nom ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('nom')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="code" value="Code" />
    <x-text-input id="code" name="code" type="text" class="mt-1 block w-full"
        :value="old('code', $filiere->code ?? '')" required />
    <x-input-error :messages="$errors->get('code')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="description" value="Description (facultatif)" />
    <textarea id="description" name="description" rows="3"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $filiere->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="mt-6 flex items-center gap-4">
    <x-primary-button>Enregistrer</x-primary-button>
    <a href="{{ route('admin.filieres.index') }}" class="text-sm text-gray-600 hover:underline">Annuler</a>
</div>