<div class="col-lg-4">
    <h3 class="section-subtitle">{{ $category->name }}</h3>
    <div class="news-card">
        @foreach ($category->posts as $article)
            <x-article-component :article="$article" />
        @endforeach
    </div>
    <a href="{{ route('posts.index', ['category' => $category->slug]) }}" class="btn btn-primary mt-4">Voir plus</a>
</div>
