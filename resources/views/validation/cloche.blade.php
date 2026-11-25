{{-- Cloche de notifications.
     À inclure dans la barre de navigation par Béni (layouts/app.blade.php) :
     @auth @include('validation.cloche') @endauth --}}
@php
    $nombreNonLues = auth()->user()->unreadNotifications()->count();
@endphp
<a href="{{ route('validation.notifications.index') }}" class="cloche" aria-label="Notifications">
    <span class="cloche-icone">&#128276;</span>
    @if ($nombreNonLues > 0)
        <span class="cloche-pastille">{{ $nombreNonLues }}</span>
    @endif
</a>

