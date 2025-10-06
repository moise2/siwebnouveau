@extends('frontend.layouts.page', [
    'pageTitle' => 'Actualités Sociales',
    'pageId' => 'articles'
])

@section('page-content')
    <h2 class="card-title">Actualités Sociales</h2>

    <div class="search-input-group mb-3">
        <input id="search-input" class="form-control search_input" type="text" placeholder="Rechercher des articles...">
        <button id="search-button" class="btn btn-danger search_icon" type="button">
            <i class="fas fa-search"></i>
        </button>
    </div>

    <div id="progress-container" class="progress-container" style="display: none;">
        <div id="progress-bar" class="progress-bar"></div>
    </div>

    <div class="dropdown-container mt-4">
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" id="dropdownMenuButtonAnnee" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Années <span id="annee-badge" class="badge bg-danger ms-2"></span>
            </button>
            <div id="annee-dropdown" class="dropdown-menu"></div>
        </div>
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" id="dropdownMenuButtonMois" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Mois <span id="mois-badge" class="badge bg-danger ms-2"></span>
            </button>
            <div id="mois-dropdown" class="dropdown-menu"></div>
        </div>
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" id="dropdownMenuButtonCategorie" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Catégories <span id="categorie-badge" class="badge bg-danger ms-2"></span>
            </button>
            <div id="categorie-dropdown" class="dropdown-menu"></div>
        </div>
    </div>

    <div class="table-container mt-4">
        <div class="row" id="results-container">
            @foreach($articlesociales as $article)
                <div class="col-12 col-md-4 mb-4 article-card" data-category="{{ $article->category }}">
                    <div class="card h-100">
                        <a href="{{ route('sociale.show', ['slug' => $article->slug]) }}">
                            <img src="{{ asset('storage/' . $article->image) }}" class="card-img-top" alt="{{ htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8') }}">
                        </a>

                        <div class="card-body">
                            <a href="{{ route('sociale.show', ['slug' => $article->slug]) }}" class="text-decoration-none">
                                <h3 class="card-title">{{ htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8') }}</h3>
                            </a>

                            <p class="card-date">Publié le {{ $article->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div id="error-message" class="text-danger" style="display: none;">Aucun résultat trouvé.</div>
        <div class="mt-4">
            {{ $articlesociales->links('vendor.pagination.bootstrap-4') }}
        </div>
        <p>{{ $articlesociales->count() }} articles affichés sur cette page.</p>
        <p>Total des articles : {{ $articlesociales->total() }}</p>
    </div>

    <script>
        const assetUrl = "{{ asset('storage/') }}";
        const searchUrl = "{{ route('sociale.search') }}";
    </script>
@endsection
