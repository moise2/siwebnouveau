<div class="col-lg-4">
    <h3 class="section-subtitle">{{ $category->name }}</h3> <div class="news-card">
        @foreach ($articles as $article)
            <a href="{{ route('posts.show', $article->slug) }}" class="news-link" style="background-image: url('{{ Voyager::image($article->image) }}');">
                <div class="news-overlay">
                    <div class="news-title">{{ $article->title }}</div>
                    <div class="news-date">Publié le {{ $article->created_at->format('d/m/Y') }}</div>
                </div>
            </a>
        @endforeach
    </div>
    <a href="{{ route('posts.index', ['category' => $category->slug]) }}" class="btn btn-primary mt-4">Voir plus</a>
</div>
