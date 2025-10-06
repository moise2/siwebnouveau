<div id="contentSlider" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        @php
            $active = true;
        @endphp

        @foreach ($slides['articles'] as $article)
            <div class="carousel-item {{ $active ? 'active' : '' }}">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $article->title }}</h5>
                        <p class="card-text">{{ Str::limit($article->content, 150) }}</p>
                        <a href="{{ route('articles.show', $article->id) }}" class="btn btn-primary">Lire plus</a>
                    </div>
                </div>
            </div>
            @php $active = false; @endphp
        @endforeach

        @foreach ($slides['documents'] as $document)
            <div class="carousel-item {{ $active ? 'active' : '' }}">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $document->title }}</h5>
                        <p class="card-text">{{ Str::limit($document->description, 150) }}</p>
                        <a href="{{ route('documents.show', $document->id) }}" class="btn btn-primary">Voir document</a>
                    </div>
                </div>
            </div>
            @php $active = false; @endphp
        @endforeach

        @foreach ($slides['events'] as $event)
            <div class="carousel-item {{ $active ? 'active' : '' }}">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $event->title }}</h5>
                        <p class="card-text">{{ Str::limit($event->description, 150) }}</p>
                        <a href="{{ route('events.show', $event->id) }}" class="btn btn-primary">Détails de l'événement</a>
                    </div>
                </div>
            </div>
            @php $active = false; @endphp
        @endforeach
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#contentSlider" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#contentSlider" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
