@extends('frontend.layouts.app')

@section('title', $event ? $event->title : 'Événement non trouvé')

@section('content')
<div id="custom-article">
    <div class="container-fluid">
        @if($event)
            {{-- Image principale --}}
            @if($event->featured_image)
                <div class="blue-band">
                    <img src="{{ asset('storage/' . $event->featured_image) }}" alt="{{ $event->title }}" class="featured-image img-fluid">
                </div>
            @endif

            <div class="content-divider"></div>

            <div class="container article-content">
                <div class="row">
                    {{-- Contenu principal --}}
                    <div class="col-md-12 right-column">
                        <h1 class="article-title">{{ $event->title }}</h1>
                        <div class="underline mb-3"></div>
                        <p class="article-summary text-muted">
                            <em>{{ $event->summary }}</em>
                        </p>

                        {{-- Contenu principal de l'événement --}}
                        <div class="event-body mb-4">
                            {!! $event->body !!}
                        </div>

                        {{-- Liens de navigation --}}
                        <div class="nav-links my-4">
                            @if ($previousEvent)
                                <a href="{{ route('events.show', $previousEvent->slug) }}" class="btn btn-link">&laquo; Événement précédent</a>
                            @endif

                            @if ($nextEvent)
                                <a href="{{ route('events.show', $nextEvent->slug) }}" class="btn btn-link ">Événement suivant &raquo;</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning">
                Événement non trouvé. <a href="{{ route('agenda.index') }}" class="alert-link">Retour à l'agenda</a>.
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const progressBar = document.getElementById('progressBar');

    if (progressBar) {
        document.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrollPercent = (scrollTop / scrollHeight) * 100;

            progressBar.style.width = scrollPercent + '%';
        });
    }
});
</script>

@endsection
