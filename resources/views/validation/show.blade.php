@extends('layouts.app')

@push('styles')
    @vite('resources/css/validation.css')
@endpush

@section('content')
<div class="validation-page">
    <a class="validation-lien" href="{{ route('validation.index') }}">&larr; Retour à la liste</a>

    <h1 class="validation-titre">{{ $memoire->titre }}</h1>

    @if (session('succes'))
        <div class="validation-alerte validation-alerte--succes">{{ session('succes') }}</div>
    @endif

    @if ($errors->any())
        <div class="validation-alerte validation-alerte--erreur">
            <ul>
                @foreach ($errors->all() as $erreur)
                    <li>{{ $erreur }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="validation-carte">
        <p>
            <strong>Statut :</strong>
            <span class="validation-badge validation-badge--{{ $memoire->statut }}">
                {{ ucfirst(str_replace('_', ' ', $memoire->statut)) }}
            </span>
        </p>
        <p><strong>Étudiant :</strong> {{ $memoire->etudiant->name ?? '—' }}</p>
        <p><strong>Filière :</strong> {{ $memoire->filiere->nom ?? '—' }}</p>
        <p><strong>Résumé :</strong> {{ $memoire->resume }}</p>
        @if (! is_null($memoire->score_similarite))
            <p>
                <strong>Similarité maximale :</strong> {{ $memoire->score_similarite }} %
                @if ($memoire->memoire_similaire_id)
                    avec le mémoire n° {{ $memoire->memoire_similaire_id }}
                    (<a class="validation-lien" href="{{ route('validation.show', $memoire->memoire_similaire_id) }}">voir</a>)
                @endif
            </p>
        @endif
        @if ($memoire->statut === 'rejete' && $memoire->motif_rejet)
            <p><strong>Motif du rejet :</strong> {{ $memoire->motif_rejet }}</p>
        @endif
        {{-- Route fournie par Emma (lecture en ligne) --}}
        @if (Route::has('lecteur.lire'))
            <p><a class="validation-lien" href="{{ route('lecteur.lire', $memoire) }}" target="_blank">Lire le PDF</a></p>
        @endif
    </section>

    {{-- Décision --}}
    <section class="validation-carte">
        <h2>Décision</h2>

        @if (in_array('en_verification', $transitions, true))
            <form method="POST" action="{{ route('validation.statuer', $memoire) }}">
                @csrf
                <input type="hidden" name="statut" value="en_verification">
                <button type="submit" class="validation-bouton">Prendre en charge</button>
            </form>
        @endif

        @if (in_array('valide', $transitions, true))
            <form method="POST" action="{{ route('validation.statuer', $memoire) }}" class="validation-form">
                @csrf
                <input type="hidden" name="statut" value="valide">
                <button type="submit" class="validation-bouton validation-bouton--succes">Valider le mémoire</button>
            </form>

            <form method="POST" action="{{ route('validation.statuer', $memoire) }}" class="validation-form">
                @csrf
                <input type="hidden" name="statut" value="rejete">
                <label for="motif_rejet">Motif du rejet</label>
                <textarea id="motif_rejet" name="motif_rejet" rows="4" required>{{ old('motif_rejet') }}</textarea>
                <button type="submit" class="validation-bouton validation-bouton--danger">Rejeter le mémoire</button>
            </form>
        @endif

        @if (empty($transitions))
            <p class="validation-vide">Ce mémoire a déjà été traité : aucune action de statut n'est possible.</p>
        @endif
    </section>

    {{-- Téléchargement --}}
    <section class="validation-carte">
        <h2>Téléchargement</h2>
        <p>
            Actuellement :
            <strong>{{ $memoire->telechargement_autorise ? 'autorisé' : 'désactivé' }}</strong>
        </p>
        <form method="POST" action="{{ route('validation.telechargement', $memoire) }}">
            @csrf
            @method('PATCH')
            <button type="submit" class="validation-bouton">
                {{ $memoire->telechargement_autorise ? 'Désactiver le téléchargement' : 'Activer le téléchargement' }}
            </button>
        </form>
    </section>

    {{-- Historique --}}
    <section class="validation-carte">
        <h2>Historique des décisions</h2>
        <ul class="validation-historique">
            @forelse ($memoire->validations->sortByDesc('created_at') as $validation)
                <li>
                    {{ $validation->created_at->format('d/m/Y H:i') }} —
                    {{ $validation->validateur->name ?? 'Système (détection automatique)' }} :
                    {{ str_replace('_', ' ', $validation->statut_precedent) }}
                    &rarr; {{ str_replace('_', ' ', $validation->statut_nouveau) }}
                    @if ($validation->motif_rejet)
                        <em>(motif : {{ $validation->motif_rejet }})</em>
                    @endif
                </li>
            @empty
                <li class="validation-vide">Aucune décision enregistrée.</li>
            @endforelse
        </ul>
    </section>
</div>
@endsection
