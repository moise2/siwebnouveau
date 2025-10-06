// resources/views/components/news-card.blade.php
<div class="news-card">
    <a href="{{ $article->url }}" class="news-link" style="background-image: url('{{ $article->image }}');">
        <div class="news-category">{{ $article->category }}</div>
        <div class="news-overlay">
            <div class="news-title">{{ $article->title }}</div>
            <div class="news-date">Publié le {{ $article->date }}</div>
        </div>
    </a>
</div>


