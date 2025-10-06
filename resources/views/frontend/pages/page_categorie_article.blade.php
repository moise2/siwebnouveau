@extends('frontend.layouts.page', [
    'pageTitle' => $category->name,  // Dynamiser le titre de la page
    'pageId' => 'categorie-article'
])

@section('page-content')
    {{-- Définir le contenu de la section pour le titre --}}
    @section('titre_page_contenu')
        Catégorie : {{ $category->name }}  // Afficher le nom de la catégorie
    @endsection

    {{-- Définir le contenu de la section pour le texte principal --}}
    @section('contenu_page')

        <div class="search-input-group mb-3">
            <input id="search-input" class="form-control search_input" type="text" placeholder="Rechercher des articles...">
            <button id="search-button" class="btn btn-danger search_icon" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <div class="progress-container mb-3">
            <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>

        <div class="table-container mt-4">
            <div class="row" id="resultats">
                @forelse ($articles as $article)
                    <div class="col-12 col-md-4 mb-4 article-card" data-category="{{ $article->category->slug }}">
                        <div class="card h-100">
                            <img src="{{ Voyager::image($article->image) }}" class="card-img-top" alt="{{ $article->title }}">
                            <div class="card-body">
                                <h3 class="card-title">{{ $article->title }}</h3>
                                <p class="card-date">Publié le {{ $article->created_at->format('d M Y') }}</p>
                                <a href="{{ route('posts.show', $article->slug) }}" class="btn btn-primary">Lire l'article</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div id="error-message" class="text-center">Aucun article trouvé dans cette catégorie.</div>
                @endforelse
            </div>
            <div id="pagination" class="mt-3 d-flex justify-content-center">
                {{ $articles->links() }}
            </div>
        </div>

    @endsection

    {{-- Inclure la carte de contenu --}}
    @include('frontend.components.content-card')
@endsection
