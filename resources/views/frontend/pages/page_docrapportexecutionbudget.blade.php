@extends('frontend.layouts.page', [
    'pageTitle' => 'Rapport d\'exécution du budget',
    'pageId' => 'documents'
])

@section('page-content')
    <h2 class="card-title">Rapport d'exécution du budget</h2>

    <div class="search-input-group mb-3">
        <input id="search-input" class="form-control search_input" type="text" placeholder="Rechercher des documents...">
        <button id="search-button" class="btn btn-danger search_icon" type="button">
            <i class="fas fa-search"></i>
        </button>
    </div>

    <div id="progress-container" class="progress-container" style="display: none;">
        <div id="progress-bar" class="progress-bar"></div>
    </div>

    <div class="table-container mt-4">
        <div class="row" id="resultats">
            @foreach($documents as $document)
                <div class="col-12 col-md-4 col-lg-3 mb-4 document-card">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <a href="{{ route('documents.show', ['slug' => $document->slug]) }}" class="text-decoration-none">
                                <i class="fas fa-file-alt"></i>
                                <h3 class="card-title">{{ htmlspecialchars($document->title, ENT_QUOTES, 'UTF-8') }}</h3>
                            </a>
                            <p class="card-date">Publié le {{ $document->date_publication->format('d M Y') }}</p>
                            <a href="{{ route('documents.download', ['id' => $document->id]) }}" class="btn btn-danger mt-auto">
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

            function updateProgress(percentage) {
                progressContainer.style.display = 'block';
                progressBar.style.width = percentage + '%';
            }

            function clearProgress() {
                progressContainer.style.display = 'none';
                progressBar.style.width = '0%';
            }

            function fetchDocuments() {
                const query = searchInput.value.toLowerCase();
                updateProgress(90);

                fetch('{{ route("docrapportexecutionbudget.search") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ query })
                })
                .then(response => response.json())
                .then(data => {
                    updateProgress(100);
                    if (data.length) {
                        resultsContainer.innerHTML = data.map(document =>
                            `<div class="col-12 col-md-4 col-lg-3 mb-4 document-card">
                                <div class="card h-100">
                                    <div class="card-body d-flex flex-column">
                                        <a href="/documents/${document.slug}" class="text-decoration-none">
                                            <i class="fas fa-file-alt"></i>
                                            <h3 class="card-title">${document.title}</h3>
                                        </a>
                                        <p class="card-date">Publié le ${new Date(document.date_publication).toLocaleDateString('fr-FR')}</p>
                                        <a href="/documents/download/${document.id}" class="btn btn-danger mt-auto">
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

            searchInput.addEventListener('input', () => {
                if (searchInput.value.trim() === '') {
                    location.reload();
                } else {
                    fetchDocuments();
                }
            });

            document.querySelector('#search-button').addEventListener('click', fetchDocuments);
        });
    </script>
@endsection