@extends('frontend.layouts.page', [
    'pageTitle' => 'Actualités',
    'pageId' => 'articles'
])

@section('page-content')
    <h2 class="card-title">Actualités sur les réformes</h2>

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
            @foreach($articleactualites as $article)
                <div class="col-12 col-md-4 mb-4 article-card" data-category="{{ $article->category }}">
                    <div class="card h-100">
                        <a href="{{ route('actualites.show', ['slug' => $article->slug]) }}">
                            <img src="{{ asset('storage/' . $article->image) }}" class="card-img-top" alt="{{ htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8') }}">
                        </a>

                        <div class="card-body">
                            <a href="{{ route('actualites.show', ['slug' => $article->slug]) }}" class="text-decoration-none">
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
            {{ $articleactualites->links('vendor.pagination.bootstrap-4') }}
        </div>
        <p>{{ $articleactualites->count() }} articles affichés sur cette page.</p>
        <p>Total des articles : {{ $articleactualites->total() }}</p>
    </div>

    <script>
        const assetUrl = "{{ asset('storage/') }}";
    </script>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.querySelector('#search-input');
            const resultsContainer = document.querySelector('#results-container');
            const progressContainer = document.querySelector('#progress-container');
            const progressBar = document.querySelector('#progress-bar');
            const errorMessage = document.querySelector('#error-message');
            const dropdownMenus = {
                annee: document.querySelector('#annee-dropdown'),
                mois: document.querySelector('#mois-dropdown'),
                categorie: document.querySelector('#categorie-dropdown')
            };

            const currentYear = new Date().getFullYear();
            const months = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
            const categories = ["Technologie", "Économie", "Société", "Science", "Politique"];

            function populateDropdowns() {
                for (let year = currentYear; year >= 2000; year--) {
                    dropdownMenus.annee.innerHTML += `<a class="dropdown-item" href="#" data-value="${year}"><input type="checkbox" class="me-2" /> ${year}</a>`;
                }

                months.forEach(month => {
                    dropdownMenus.mois.innerHTML += `<a class="dropdown-item" href="#" data-value="${month}"><input type="checkbox" class="me-2" /> ${month}</a>`;
                });

                categories.forEach(category => {
                    dropdownMenus.categorie.innerHTML += `<a class="dropdown-item" href="#" data-value="${category}"><input type="checkbox" class="me-2" /> ${category}</a>`;
                });
            }

            function updateProgress(percentage) {
                progressContainer.style.display = 'block';
                progressBar.style.width = percentage + '%';
            }

            function clearProgress() {
                progressContainer.style.display = 'none';
                progressBar.style.width = '0%';
            }

            function updateSelections() {
                const selectedItems = [];
                Object.keys(dropdownMenus).forEach(key => {
                    const selectedOptions = Array.from(dropdownMenus[key].querySelectorAll('.dropdown-item input:checked')).map(item => item.parentElement.dataset.value);
                    selectedItems.push(...selectedOptions);

                    const badge = document.querySelector(`#${key}-badge`);
                    badge.textContent = selectedOptions.length ? selectedOptions.length : '';
                });

                return selectedItems;
            }

            function fetchArticles() {
                const query = searchInput.value.toLowerCase();
                const selectedItems = updateSelections();

                updateProgress(90);

                fetch('/actualites/search', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        query,
                        filters: selectedItems
                    })
                })
                .then(response => response.json())
                .then(data => {
                    updateProgress(100);

                    if (data.length) {
                        resultsContainer.innerHTML = data.map(article => `
                            <div class="col-12 col-md-4 mb-4 article-card" data-category="${article.category}">
                                <div class="card h-100">
                                    <img src="${assetUrl}/${article.image}" class="card-img-top" alt="${article.title}">
                                    <div class="card-body">
                                        <h3 class="card-title">${article.title}</h3>
                                        <p class="card-date">Publié le ${article.created_at}</p>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                        errorMessage.style.display = 'none';
                    } else {
                        resultsContainer.innerHTML = '';
                        errorMessage.style.display = 'block';
                    }

                    setTimeout(clearProgress, 1000);
                })
                .catch(error => {
                    console.error('Error:', error);
                    clearProgress();
                });
            }

            function setupDropdowns() {
                document.querySelectorAll('.dropdown-item').forEach(item => {
                    item.addEventListener('click', function(event) {
                        event.preventDefault();
                        const checkbox = this.querySelector('input[type="checkbox"]');
                        checkbox.checked = !checkbox.checked; // Toggle checkbox state
                        fetchArticles(); // Met à jour les articles lors de la sélection dans le dropdown
                    });
                });
            }

            function resetArticles() {
                resultsContainer.innerHTML = ''; // Réinitialise le contenu des articles
                fetch('/actualites') // Assurez-vous que cette route existe pour renvoyer tous les articles
                    .then(response => response.json())
                    .then(data => {
                        resultsContainer.innerHTML = data.map(article => `
                            <div class="col-12 col-md-4 mb-4 article-card" data-category="${article.category}">
                                <div class="card h-100">
                                    <img src="${assetUrl}/${article.image}" class="card-img-top" alt="${article.title}">
                                    <div class="card-body">
                                        <h3 class="card-title">${article.title}</h3>
                                        <p class="card-date">Publié le ${article.created_at}</p>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    })
                    .catch(error => {
                        console.error('Error fetching all articles:', error);
                    });

                Object.keys(dropdownMenus).forEach(key => {
                    const badge = document.querySelector(`#${key}-badge`);
                    badge.textContent = ''; // Réinitialiser le badge
                    dropdownMenus[key].querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                        checkbox.checked = false; // Déselectionner tous les checkboxes
                    });
                });
            }

            searchInput.addEventListener('input', fetchArticles);
            document.querySelector('#search-button').addEventListener('click', fetchArticles);
            populateDropdowns();
            setupDropdowns();
            resetArticles(); // Récupérer tous les articles à la première charge
        });
    </script> --}}
@endsection
