@extends('frontend.layouts.page', [
    'pageTitle' => $title,
    'pageId' => 'articles'
])

@section('page-content')
    <h2 class="card-title">{{ $title }}</h2>

    <!-- Barre de recherche -->
    <div class="search-input-group mb-3">
        <input id="search-input" class="form-control search_input" type="text" placeholder="Rechercher des articles...">
        <button id="search-button" class="btn btn-danger search_icon" type="button">
            <i class="fas fa-search"></i>
        </button>
    </div>

    <div id="selectedCategories" class="selected-items"></div>

    <!-- Progression -->
    <div id="progress-container" class="progress-container" style="display: none;">
        <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
    </div>

    <!-- Dropdowns -->
    <div class="dropdown-container mt-4">
        <div class="dropdown mt-2">
            <button class="btn btn-secondary dropdown-toggle" id="dropdownMenuButtonCategorie" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Catégories <span id="categorie-badge" class="badge bg-danger ms-2"></span>
            </button>
            <div id="categorie-dropdown" class="dropdown-menu">
                @if($autoSelectedCount === 0)
                {{-- Ne rien renvoyer si le nombre est 0 --}}
            @elseif($autoSelectedCount === 1)
                @foreach($articles->pluck('categories')->flatten()->unique('id') as $category)
                    @if(in_array($category->id, $autoSelectedCategories))
                        <a class="dropdown-item" href="#" data-value="{{ $category->id }}">
                            <input type="checkbox" class="me-2" checked disabled/> {{ $category->name }}
                        </a>
                    @endif
                @endforeach
            @else
                {{-- Si le nombre est supérieur à 1, afficher toutes les catégories --}}
                @foreach($articles->pluck('categories')->flatten()->unique('id') as $category)
                    <a class="dropdown-item" href="#" data-value="{{ $category->id }}">
                        <input type="checkbox" class="me-2" checked disabled/> {{ $category->name }}
                    </a>
                @endforeach
            @endif

            </div>
        </div>

        <div class="dropdown mt-2">
            <button class="btn btn-secondary dropdown-toggle" id="dropdownMenuButtonMois" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Mois <span id="mois-badge" class="badge bg-danger ms-2"></span>
            </button>
            <div id="mois-dropdown" class="dropdown-menu">
                @foreach(['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'] as $key => $month)
                    <a class="dropdown-item"  data-value="{{ ($key+1) }}">
                        <input type="checkbox" class="me-2" /> {{ $month }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="dropdown mt-2">
            <button class="btn btn-secondary dropdown-toggle" id="dropdownMenuButtonAnnee" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Années <span id="annee-badge" class="badge bg-danger ms-2"></span>
            </button>
            <div id="annee-dropdown" class="dropdown-menu">
                @php
                    $currentYear = date('Y');
                @endphp
                @for($year = $currentYear; $year >= 2000; $year--)
                    <a class="dropdown-item" href="#" data-value="{{ $year }}">
                        <input type="checkbox" class="me-2" /> {{ $year }}
                    </a>
                @endfor
            </div>
        </div>
    </div>

    <!-- Conteneur des résultats -->
    <div class="table-container mt-4">
        <div class="row" id="resultats">
            @foreach($articles as $article)
                <div class="col-12 col-md-4 mb-4 article-card" data-category="{{ $article->category }}">
                    <div class="card h-100">
                        <!-- Lien enveloppant l'image -->
                        <a href="{{ route('articles.show', ['slug' => $article->slug]) }}">
                            <img src="{{ asset('storage/' . $article->image) }}" class="card-img-top" alt="{{ htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8') }}">
                        </a>

                        <div class="card-body">
                            <!-- Lien enveloppant le titre -->
                            <a href="{{ route('articles.show', ['slug' => $article->slug]) }}" class="text-decoration-none">
                                 <h3 class="card-title">{{ ucfirst(strtolower($article->title)) }}</h3>
                                {{-- <h3 class="card-title">{{ htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8') }}</h3> --}}
                            </a>

                            <p class="card-date">Publié le  {{ \Carbon\Carbon::parse($article->published_at)->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div id="error-message" class="text-danger" style="display: none;">Aucun résultat trouvé.</div>
        <div class="mt-4">
            {{ $articles->links('vendor.pagination.bootstrap-4') }}
        </div>
        <p id="article_count">{{ $articles->count() }} articles affichés sur cette page.</p>
        <p>Total des articles : {{ $articles->total() }}</p>
    </div>



    <script>
        const assetUrl = "{{ asset('storage/') }}";
    </script>

    <!-- Script pour la recherche avec filtres -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('#search-input');
    const searchButton = document.querySelector('#search-button');
    const resultsContainer = document.querySelector('#resultats');
    const errorMessage = document.querySelector('#error-message');
    const selectedCategoriesContainer = document.querySelector('#selectedCategories');

    const categoriesDropdown = document.querySelector('#categorie-dropdown');
    const categoriesBadge = document.querySelector('#categorie-badge');
    const moisDropdown = document.querySelector('#mois-dropdown');
    const moisBadge = document.querySelector('#mois-badge');
    const anneeDropdown = document.querySelector('#annee-dropdown');
    const anneeBadge = document.querySelector('#annee-badge');

    const progressBar = document.querySelector('#progress-bar');
    const progressContainer = document.querySelector('#progress-container');

    const loadingIndicator = document.createElement('div');
    loadingIndicator.textContent = 'Recherche en cours...';
    loadingIndicator.style.display = 'none';
    loadingIndicator.style.fontWeight = 'bold';
    resultsContainer.appendChild(loadingIndicator);

    function updateBadgeCount(dropdownId, badgeElement) {
        const checkedCount = Array.from(document.querySelectorAll(`${dropdownId} input[type="checkbox"]:checked`)).length;
        badgeElement.textContent = checkedCount > 0 ? checkedCount : '';
    }

    function updateSelectedCategories() {
        selectedCategoriesContainer.innerHTML = '';
        const categories = Array.from(document.querySelectorAll('#categorie-dropdown input:checked'))
            .map(input => {
                const categoryLink = input.closest('a');
                return categoryLink ? categoryLink.textContent.trim() : '';
            }).filter(text => text !== '');

        const months = Array.from(document.querySelectorAll('#mois-dropdown input:checked'))
            .map(input => input.closest('a').textContent.trim()).join(', ');

        const years = Array.from(document.querySelectorAll('#annee-dropdown input:checked'))
            .map(input => input.closest('a').textContent.trim()).join(', ');

        if (categories.length > 0) {
            selectedCategoriesContainer.innerHTML += `<p><strong>Catégories :</strong> ${categories.join(', ')}</p>`;
        }
        if (months) {
            selectedCategoriesContainer.innerHTML += `<p><strong>Mois :</strong> ${months}</p>`;
        }
        if (years) {
            selectedCategoriesContainer.innerHTML += `<p><strong>Années :</strong> ${years}</p>`;
        }
    }

    function fetchArticles() {
        const query = searchInput.value.toLowerCase();
        const filters = {
            categories: Array.from(document.querySelectorAll('#categorie-dropdown input:checked')).map(input => input.closest('a').dataset.value),
            months: Array.from(document.querySelectorAll('#mois-dropdown input:checked')).map(input => input.closest('a').dataset.value),
            years: Array.from(document.querySelectorAll('#annee-dropdown input:checked')).map(input => input.closest('a').dataset.value)
        };

        loadingIndicator.style.display = 'block'; // Afficher l'indicateur de chargement
        progressContainer.style.display = 'block'; // Afficher la barre de progression
        progressBar.style.width = '0%'; // Réinitialiser la barre de progression

        const searchUrl = "{{ route('articles.search') }}";

        fetch(searchUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ query, filters })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur HTTP, statut : ' + response.status);
                }
                progressBar.style.width = '50%'; // Mise à jour de la barre de progression
                return response.json();
            })
            .then(data => {
                progressBar.style.width = '100%'; // Terminer la progression
                loadingIndicator.style.display = 'none'; // Masquer l'indicateur de chargement
                progressContainer.style.display = 'none'; // Masquer la barre de progression

                $('#article_count').html(data.articles.length);
                if (data && data.articles && data.articles.length > 0) {
                    renderArticles(data.articles);
                    errorMessage.style.display = 'none';
                } else {
                    showErrorMessage('Aucun article trouvé.');
                }
            })
            .catch(error => {
                progressBar.style.width = '100%'; // Terminer la progression même en cas d'erreur
                showErrorMessage('Une erreur est survenue lors de la récupération des articles.');
            });
    }

    function renderArticles(articles) {
        resultsContainer.innerHTML = articles.map(article => `
            <div class="col-12 col-md-4 mb-4 article-card" data-category="${article.category}">
                <div class="card h-100">
                    <a href="/articles/${article.slug}">
                        <img src="${article.file_url}" class="card-img-top" alt="${article.title}">
                    </a>
                    <div class="card-body">
                        <a href="/articles/${article.slug}" class="text-decoration-none">
                            <h3 class="card-title">${article.title}</h3>
                        </a>
                        <p class="card-date">Publié le ${new Date(article.published_at).toLocaleDateString('fr-FR', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        })}</p>
                    </div>
                </div>
            </div>
        `).join('');
        errorMessage.style.display = 'none';
    }

    function showErrorMessage(message) {
        errorMessage.textContent = message;
        errorMessage.style.display = 'block';
        resultsContainer.innerHTML = '';
    }

    // Attacher les événements pour les filtres
    categoriesDropdown.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            updateBadgeCount('#categorie-dropdown', categoriesBadge);
            updateSelectedCategories();
            fetchArticles();
        });
    });

    moisDropdown.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            updateBadgeCount('#mois-dropdown', moisBadge);
            updateSelectedCategories();
            fetchArticles();
        });
    });

    anneeDropdown.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            updateBadgeCount('#annee-dropdown', anneeBadge);
            updateSelectedCategories();
            fetchArticles();
        });
    });

    // Recherche via le bouton ou la saisie dans le champ
    searchInput.addEventListener('input', fetchArticles);
    searchButton.addEventListener('click', fetchArticles);
});
        </script>


@endsection
