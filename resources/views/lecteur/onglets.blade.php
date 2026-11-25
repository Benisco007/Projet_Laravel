<div class="mb-6 flex gap-2 text-sm">
    @foreach (['lecteur.catalogue' => 'Catalogue', 'lecteur.favoris' => 'Mes favoris', 'lecteur.historique' => 'Mon historique'] as $nomRoute => $libelle)
        <a href="{{ route($nomRoute) }}"
            class="rounded-md px-3 py-1.5 {{ request()->routeIs($nomRoute) ? 'bg-gray-800 text-white' : 'bg-white text-gray-700 shadow hover:bg-gray-50' }}">
            {{ $libelle }}
        </a>
    @endforeach
</div>