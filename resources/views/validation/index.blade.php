<x-app-layout>
    @vite('resources/css/validation.css')
<div class="validation-page">
    <h1 class="validation-titre">Mémoires à traiter</h1>

    @if (session('succes'))
        <div class="validation-alerte validation-alerte--succes">{{ session('succes') }}</div>
    @endif

    <nav class="validation-filtres" aria-label="Filtrer par statut">
        <a href="{{ route('validation.index') }}"
           class="validation-filtre {{ $statutActif ? '' : 'validation-filtre--actif' }}">
            Tous
        </a>
        @foreach (config('memoire.statuts') as $statut)
            <a href="{{ route('validation.index', ['statut' => $statut]) }}"
               class="validation-filtre {{ $statutActif === $statut ? 'validation-filtre--actif' : '' }}">
                {{ ucfirst(str_replace('_', ' ', $statut)) }}
                <span class="validation-compteur">{{ $compteurs[$statut] ?? 0 }}</span>
            </a>
        @endforeach
    </nav>

    <table class="validation-table">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Étudiant</th>
                <th>Filière</th>
                <th>Déposé le</th>
                <th>Statut</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($memoires as $memoire)
                <tr>
                    <td>{{ $memoire->titre }}</td>
                    <td>{{ $memoire->etudiant->name ?? '—' }}</td>
                    <td>{{ $memoire->filiere->nom ?? '—' }}</td>
                    <td>{{ $memoire->created_at->format('d/m/Y') }}</td>
                    <td>
                        <span class="validation-badge validation-badge--{{ $memoire->statut }}">
                            {{ ucfirst(str_replace('_', ' ', $memoire->statut)) }}
                        </span>
                    </td>
                    <td>
                        <a class="validation-lien" href="{{ route('validation.show', $memoire) }}">Examiner</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="validation-vide">Aucun mémoire pour ce filtre.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="validation-pagination">
        {{ $memoires->links() }}
    </div>
</div>
</x-app-layout>
