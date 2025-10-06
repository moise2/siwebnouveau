@extends('frontend.layouts.page', [
    'pageTitle' => 'Budget Citoyen de l\'État',
    'pageId' => 'documents'
])

@section('page-content')
    <h2 class="card-title">Budget Citoyen de l'État</h2>

    <div class="search-input-group mb-3">
        <input id="search-input" class="form-control search_input" type="text" placeholder="Rechercher des documents...">
        <button id="search-button" class="btn btn-danger search_icon" type="button">
            <i class="fas fa-search"></i>
        </button>
    </div>

    <div id="progress-container" class="progress-container" style="display: none;">
        <div id="progress-bar" class="progress-bar"></div>
    </div>

    <div class="dropdown-container mt-4">
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" id="dropdownMenuButtonCategorie" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Catégories <span id="categorie-badge" class="badge bg-danger ms-2"></span>
            </button>
            <div id="categorie-dropdown" class="dropdown-menu"></div>
        </div>
    </div>

    <div class="table-container mt-4">
        <div class="row" id="resultats">
            @foreach($documents as $document)
                <div class="col-12 col-md-4 col-lg-3 mb-4 document-card" data-category="{{ $document->category }}">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <a href="{{ route('documents.show', ['slug' => $document->slug]) }}" class="text-decoration-none">
                                <i class="fas fa-file-alt"></i>
                                <h3 class="card-title">{{ htmlspecialchars($document->title, ENT_QUOTES, 'UTF-8') }}</h3>
                            </a>
                            <p class="card-category">Catégorie : {{ htmlspecialchars($document->category, ENT_QUOTES, 'UTF-8') }}</p>
                            <p class="card-date">Publié le {{ $document->created_at->format('d M Y') }}</p>
                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="btn btn-danger mt-auto">
                                <i class="fas fa-download"></i> Télécharger
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="error-message" class="text-danger" style="display: none;">Aucun résultat trouvé.</div>
        <div class="mt-4">
            {{ $documents->links('vendor.pagination.bootstrap-4') }}
        </div>
        <p>{{ $documents->count() }} documents affichés sur cette page.</p>
        <p>Total des documents : {{ $documents->total() }}</p>
    </div>

    <script>
        const assetUrl = "{{ asset('storage/') }}";
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.querySelector('#search-input');
            const resultsContainer = document.querySelector('#resultats');
            const progressContainer = document.querySelector('#progress-container');
            const progressBar = document.querySelector('#progress-bar');
            const errorMessage = document.querySelector('#error-message');
            const dropdownMenus = {
                categorie: document.querySelector('#categorie-dropdown')
            };

            function populateDropdowns() {
                const categories = ["Rapport", "Guide", "Document technique"];
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
                const selectedOptions = Array.from(dropdownMenus.categorie.querySelectorAll('.dropdown-item input:checked')).map(item => item.parentElement.dataset.value);
                const badge = document.querySelector('#categorie-badge');
                badge.textContent = selectedOptions.length ? selectedOptions.length : '';
                return selectedOptions;
            }

            function fetchDocuments() {
                const query = searchInput.value.toLowerCase();
                const selectedItems = updateSelections();
                updateProgress(90);

                fetch('{{ route("documents.search") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ query, filters: selectedItems })
                })
                .then(response => response.json())
                .then(data => {
                    updateProgress(100);
                    if (data.length) {
                        resultsContainer.innerHTML = data.map(document =>
    `<div class="col-12 col-md-4 col-lg-3 mb-4 document-card" data-category="${document.category}">
        <div class="card h-100">
            <div class="card-body d-flex flex-column">
                <a href="{{ url('/documents/') }}/${document.slug}" class="text-decoration-none">
                    <i class="fas fa-file-alt"></i>
                    <h3 class="card-title">${document.title}</h3>
                </a>
                <p class="card-category">Catégorie : ${document.category}</p>
                <p class="card-date">Publié le ${new Date(document.created_at).toLocaleDateString('fr-FR', {
                    day: '2-digit', month: 'short', year: 'numeric'
                })}</p>
                <a href="${assetUrl}/${document.file_path}" target="_blank" class="btn btn-danger mt-auto">
                    <i class="fas fa-download"></i> Télécharger
                </a>
            </div>
        </div>
    </div>`
).join('');

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
                        checkbox.checked = !checkbox.checked;
                        fetchDocuments();
                    });
                });
            }

            function resetDocuments() {
                resultsContainer.innerHTML = '';
                fetch('{{ route("documents.all") }}')
                    .then(response => response.json())
                    .then(data => {
                        resultsContainer.innerHTML = data.map(document =>
                            `<div class="col-12 col-md-4 col-lg-3 mb-4 document-card" data-category="${document.category}">
        <div class="card h-100">
            <div class="card-body d-flex flex-column">
                <a href="{{ url('/documents/') }}/${document.slug}" class="text-decoration-none">
                    <i class="fas fa-file-alt"></i>
                    <h3 class="card-title">${document.title}</h3>
                </a>
                <p class="card-category">Catégorie : ${document.category}</p>
                <p class="card-date">Publié le ${new Date(document.created_at).toLocaleDateString('fr-FR', {
                    day: '2-digit', month: 'short', year: 'numeric'
                })}</p>
                <a href="${assetUrl}/${document.file_path}" target="_blank" class="btn btn-danger mt-auto">
                    <i class="fas fa-download"></i> Télécharger
                </a>
            </div>
        </div>
    </div>`
                        ).join('');
                    })
                    .catch(error => {
                        console.error('Error fetching all documents:', error);
                    });

                const badge = document.querySelector('#categorie-badge');
                badge.textContent = '';
                dropdownMenus.categorie.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                    checkbox.checked = false;
                });
            }

            populateDropdowns();
            setupDropdowns();

            searchInput.addEventListener('input', () => {
                if (searchInput.value.trim() === '') {
                    resetDocuments();
                } else {
                    fetchDocuments();
                }
            });

            document.querySelector('#search-button').addEventListener('click', fetchDocuments);
        });
    </script>
@endsection
