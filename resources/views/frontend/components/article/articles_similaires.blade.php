<div class="similar-articles">
    <span>Articles similaires</span>

    <div class="row">
        <div class="col-md-12">
            @foreach ($relatedArticles as $relatedArticle)
                <div class="similar-article-card">
                    <a href="{{ route('posts.show', $relatedArticle->slug) }}" class="similar-article-link">
                        <div class="similar-article-image" style="background-image: url('{{ asset('storage/' . $relatedArticle->image) }}');"></div>
                        <div class="similar-article-content">
                            <div class="similar-article-category">{{ optional($relatedArticle->categories->first())->name }}</div>
                            <h4 class="similar-article-title">{{ html_entity_decode($relatedArticle->title) }}</h4>
                            <div class="similar-article-date">{{ $relatedArticle->created_at->format('d/m/Y') }}</div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <a href="{{ route('page_articles') }}" class="btn btn-primary mt-4">Voir plus</a>
</div>
