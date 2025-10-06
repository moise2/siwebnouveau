@extends('frontend.layouts.page', [
    'pageTitle' => 'Agenda',
    'pageId' => 'articles'
])

@section('page-content')
    <h2 class="card-title" style="color: black !important; font-size: 1.5rem !important;">Agenda</h2>

    <div class="search-input-group mb-3">
        <input id="search-input" class="form-control search_input" type="text" placeholder="Rechercher des événements...">
        <button id="search-button" class="btn btn-danger search_icon" type="button">
            <i class="fas fa-search"></i>
        </button>
    </div>

    <div id="progress-container" class="progress-container" style="display: none;">
        <div id="progress-bar" class="progress-bar"></div>
    </div>

    {{-- <div class="dropdown-container mt-4">
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
    </div> --}}

    <div class="table-container mt-4">
        <div class="row" id="resultats">
        @foreach($events as $event)
            <div class="col-12 col-md-4 mb-4 evenement-card" data-category="{{ $event->category }}">
                <div class="card h-100">
                    <a href="{{ route('events.show', ['slug' => $event->slug]) }}">
                        <img src="{{ Storage::url($event->featured_image) }}" class="card-img-top" alt="{{ htmlspecialchars($event->title, ENT_QUOTES, 'UTF-8') }}">
                    </a>
                    <div class="card-body">
                        <a href="{{ route('events.show', ['slug' => $event->slug]) }}" class="text-decoration-none text-dark d-flex align-items-center gap-2 mb-3" style="color: black;">
                            <h3 class="card-title mb-0" style="font-size: 1rem !important; color: black !important;">
                                {{ htmlspecialchars($event->title, ENT_QUOTES, 'UTF-8') }}
                            </h3>
                        </a>
                        <p class="card-date">
                            @if($event->created_at)
                                <div class="agenda-card-period">
                                    Début: {{ \Carbon\Carbon::parse($event->start_date)->format('d F Y') }}<br>
                                    Fin: {{ \Carbon\Carbon::parse($event->end_date)->format('d F Y') }}
                                </div>
                            @else
                                <span>Aucune date disponible</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
        </div>
        <div id="error-message" class="text-danger" style="display: none;">Aucun résultat trouvé.</div>
        <div class="mt-4">
            {{ $events->links('vendor.pagination.bootstrap-4') }}
        </div>
        <p>{{ $events->count() }} événements affichés sur cette page.</p>
        <p>Total des événements : {{ $events->total() }}</p>
    </div>

    <script>
        const assetUrl = "{{ asset('storage/') }}";
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.querySelector('#search-input');
            const resultsContainer = document.querySelector('#resultats');
            const errorMessage = document.querySelector('#error-message');

            function fetchEvenements(query='') {
                const encodedQuery = encodeURIComponent(query);
                fetch('/search-evenements', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ query })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.length) {
                        resultsContainer.innerHTML = data.map(event => `
                            <div class="col-12 col-md-4 mb-4 evenement-card" data-category="${event.category}">
                                <div class="card h-100">
                                    <a href="/events/${event.slug}">
                                        <img src="${assetUrl}/${event.featured_image}" class="card-img-top" alt="${event.title}">
                                    </a>
                                    <div class="card-body">
                                        <a href="/events/${event.slug}" class="text-decoration-none" style="color: black;">
                                            <h3 class="card-title" style="font-size: 1rem !important; color: black !important;">${event.title}</h3>
                                        </a>
                                        <p class="card-date">Date : ${event.start_date} - ${event.end_date}</p>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                        errorMessage.style.display = 'none';
                    } else {
                        resultsContainer.innerHTML = '';
                        errorMessage.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Erreur :', error);
                });
            }

            fetchEvenements();

            searchInput.addEventListener('input', () => {
                const query = searchInput.value.trim();
                fetchEvenements(query.length > 0 ? query : '');
            });
        });
    </script>
@endsection
