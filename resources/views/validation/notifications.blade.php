@extends('layouts.app')

@push('styles')
    @vite('resources/css/validation.css')
@endpush

@section('content')
<div class="validation-page">
    <h1 class="validation-titre">
        Mes notifications
        @if ($nombreNonLues > 0)
            <span class="validation-compteur">{{ $nombreNonLues }} non lue(s)</span>
        @endif
    </h1>

    @if ($nombreNonLues > 0)
        <form method="POST" action="{{ route('validation.notifications.tout-lire') }}">
            @csrf
            @method('PATCH')
            <button type="submit" class="validation-bouton">Tout marquer comme lu</button>
        </form>
    @endif

    <ul class="notif-liste">
        @forelse ($notifications as $notification)
            <li class="notif-item {{ $notification->read_at ? 'notif-item--lue' : 'notif-item--non-lue' }}">
                <p>{{ $notification->data['message'] ?? '' }}</p>

                @if (! empty($notification->data['motif_rejet']))
                    <p class="notif-motif"><strong>Motif du rejet :</strong> {{ $notification->data['motif_rejet'] }}</p>
                @endif

                <small>{{ $notification->created_at->diffForHumans() }}</small>

                @unless ($notification->read_at)
                    <form method="POST" action="{{ route('validation.notifications.lue', $notification->id) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="validation-lien">Marquer comme lue</button>
                    </form>
                @endunless
            </li>
        @empty
            <li class="validation-vide">Aucune notification pour le moment.</li>
        @endforelse
    </ul>

    {{ $notifications->links() }}
</div>
@endsection
