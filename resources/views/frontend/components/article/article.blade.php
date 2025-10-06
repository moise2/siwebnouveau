<div class="news-card">
    <a href="{{ route('articles.show', ['slug' => $article->slug]) }}" class="news-link" style="background-image: url('{{ asset('storage/' . $article->image) }}');">
    <div class="news-category">
            {{ optional($article->categories->first())->name ?? 'Sans Catégorie' }}
        </div>
        <div class="news-overlay">
            <div class="news-title" style="font-weight: 200;">{{ $article->title }}</div>
            <div class="news-date">Publié le {{ \Carbon\Carbon::parse($article->published_at)->format('d/m/Y') }}</div>
        </div>
    </a>
</div>
