@extends('frontend.layouts.page', [
    'pageTitle' => $title,
    'pageId' => 'articles'
])

@section('page-content')
<h2 class="card-title">{{ $title }}</h2>

<div class="search-input-group mb-3">
    <input id="search-input" class="form-control search_input" type="text" placeholder="Rechercher des documents, événements ou articles...">
    <button id="search-button" class="btn btn-danger search_icon" type="button">
        <i class="fas fa-search"></i>
    </button>
</div>

{{-- Ajout pour le nombre de résultats --}}
<p id="results-count" class="text-muted mt-2" style="display: none;"></p>

{{-- Conteneur pour afficher les filtres sélectionnés --}}
<div id="selected-items" class="d-flex flex-wrap mb-3"></div>

{{-- Barre de progression pour le chargement --}}
<div id="progress-bar-container" style="display: none;">
    <div class="progress" style="height: 10px;">
        <div id="progress-bar" class="progress-bar bg-danger" role="progressbar" style="width: 0;"></div>
    </div>
</div>

{{-- Conteneur des dropdowns de filtres --}}
<div class="dropdown-container mt-4 d-flex flex-wrap gap-3">
    @foreach([
        [
            'name' => 'tables',
            'label' => 'Cibles',
            'items' => [
                ['name' => 'posts', 'displayName' => 'Articles'],
                ['name' => 'events', 'displayName' => 'Agenda'],
                ['name' => 'documents', 'displayName' => 'Documents']
            ]
        ],
        [
            'name' => 'months',
            'label' => 'Mois',
            'items' => ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
        ],
        [
            'name' => 'years',
            'label' => 'Années',
            'items' => range(date('Y'), 2000)
        ]
    ] as $dropdown)
    <div class="dropdown mt-2">
        <button class="btn btn-secondary dropdown-toggle" id="dropdownMenuButton{{ ucfirst($dropdown['name']) }}" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{ $dropdown['label'] }} <span id="{{ $dropdown['name'] }}-badge" class="badge bg-danger ms-2">0</span>
        </button>
        <div id="{{ $dropdown['name'] }}-dropdown" class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
            @foreach($dropdown['items'] as $item)
                <a class="dropdown-item" href="#" data-value="{{ is_array($item) ? $item['name'] : $item }}">
                    <input type="checkbox" class="me-2" />
                    {{ is_array($item) ? $item['displayName'] : $item }}
                </a>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

{{-- Conteneur des résultats et pagination --}}
<div class="table-container mt-4">
    <div class="row" id="resultats"></div>
    <div id="error-message" class="text-danger" style="display: none;">Aucun résultat trouvé.</div>
    <div id="pagination" class="mt-3 d-flex justify-content-center"></div>
</div>

<script>
    /**
     * Convertit un nom de mois en chaîne en son numéro (1-12).
     * @param {string} mois Le nom du mois en français.
     * @returns {number|string} Le numéro du mois ou la valeur originale si non trouvée.
     */
    function moisStringEnNombre(mois) {
        const moisMap = {
            'Janvier': 1, 'Février': 2, 'Mars': 3,
            'Avril': 4, 'Mai': 5, 'Juin': 6,
            'Juillet': 7, 'Août': 8, 'Septembre': 9,
            'Octobre': 10, 'Novembre': 11, 'Décembre': 12
        };
        return moisMap[mois] ?? mois; // Retourne le nombre ou la valeur d'origine si non mappée (par exemple, si c'est déjà un nombre)
    }

    /**
     * Formate une chaîne de date en format "jour mois année" localisé en français.
     * @param {string|Date} dateString La chaîne de date ou un objet Date.
     * @returns {string} La date formatée ou 'N/A' en cas d'erreur.
     */
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        try {
            const date = new Date(dateString);
            // Vérifie si la date est valide. new Date("invalid date string") retourne "Invalid Date"
            if (isNaN(date.getTime())) {
                throw new Error("Date invalide");
            }
            return date.toLocaleDateString('fr-FR', options);
        } catch (e) {
            console.error("Erreur de formatage de date:", dateString, e);
            return dateString; // Retourne la chaîne originale si le formatage échoue
        }
    }

    /**
     * Formate un titre en mettant la première lettre en majuscule.
     * @param {string} title Le titre à formater.
     * @returns {string} Le titre formaté.
     */
    function formatTitle(title) {
        if (!title) return '';
        title = String(title).toLowerCase(); // Assure que c'est une chaîne
        return title.charAt(0).toUpperCase() + title.slice(1);
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Références aux éléments du DOM
        const searchInput = document.getElementById('search-input');
        const searchButton = document.getElementById('search-button');
        const resultatsContainer = document.getElementById('resultats');
        const errorMessage = document.getElementById('error-message');
        const selectedItemsContainer = document.getElementById('selected-items');
        const progressBar = document.getElementById('progress-bar');
        const progressBarContainer = document.getElementById('progress-bar-container');
        const dropdowns = document.querySelectorAll('.dropdown-menu');
        const paginationContainer = document.getElementById('pagination');
        const resultsCountElement = document.getElementById('results-count'); // Nouveau: référence au compteur de résultats

        // État des filtres et de la pagination
        let filters = { tables: [], months: [], years: [] };
        let typingTimer;
        const typingDelay = 500; // Délai avant de lancer la recherche après la frappe
        let currentPage = 1;

        /**
         * Met à jour l'affichage des filtres sélectionnés et les badges de compte.
         */
        const updateSelectedItems = () => {
            selectedItemsContainer.innerHTML = ''; // Nettoie le conteneur des filtres affichés
            Object.keys(filters).forEach(key => {
                filters[key].forEach(item => {
                    let displayName = item;
                    // Pour les tables, trouver le displayName complet à partir des options du dropdown
                    if (key === 'tables') {
                        const tableOption = Array.from(document.getElementById('tables-dropdown').querySelectorAll('.dropdown-item'))
                                                .find(el => el.dataset.value === item);
                        if (tableOption) {
                            // Extrait le texte sans la checkbox (premiers caractères et espace)
                            displayName = tableOption.textContent.trim().replace(/^\s*\S*\s*/, '');
                        }
                    }
                    selectedItemsContainer.innerHTML += `<span class="badge bg-danger me-2">${displayName} <button type="button" class="btn-close btn-close-white" aria-label="Close" data-filter-type="${key}" data-filter-value="${item}"></button></span>`;
                });
                // Met à jour le badge de chaque catégorie de filtre (tables, months, years)
                const badgeElement = document.getElementById(`${key}-badge`);
                if (badgeElement) {
                    badgeElement.textContent = filters[key].length;
                } else {
                    console.warn(`L'élément badge avec l'ID "${key}-badge" n'a pas été trouvé dans le DOM.`);
                }
            });

            // Ajoute les écouteurs d'événements pour les boutons de suppression de filtre
            selectedItemsContainer.querySelectorAll('.btn-close').forEach(button => {
                button.addEventListener('click', (event) => {
                    const filterType = event.target.dataset.filterType;
                    const filterValue = event.target.dataset.filterValue;

                    // Décoche la checkbox correspondante dans le dropdown
                    const dropdownItem = document.querySelector(`#${filterType}-dropdown .dropdown-item[data-value="${filterValue}"]`);
                    if (dropdownItem) {
                        const checkbox = dropdownItem.querySelector('input[type="checkbox"]');
                        if (checkbox) {
                            checkbox.checked = false;
                        }
                    }

                    // Supprime la valeur du filtre dans l'objet filters
                    filters[filterType] = filters[filterType].filter(v => v !== filterValue);

                    updateSelectedItems(); // Met à jour l'affichage des filtres
                    fetchResults(currentPage); // Relance la recherche avec les filtres mis à jour
                });
            });
        };

        /**
         * Rend les éléments de recherche dans le conteneur des résultats.
         * @param {Array} items Les éléments à rendre (documents, posts, events).
         * @param {string} type Le type d'éléments ('documents', 'posts', 'events').
         */
        const renderItems = (items, type) => {
            items.forEach(result => {
                let content = '';
                // Chaque type de résultat a un modèle de carte HTML différent
                switch (type) {
                    case 'documents':
                        content = `
                            <div class="col-12 col-md-4 col-lg-3 mb-4 document-card">
                                <div class="card h-100">
                                    <div class="card-body d-flex flex-column">
                                        <a href="${result.file_url}" class="text-decoration-none text-dark mb-3">
                                            <i class="fas fa-file-alt"></i>
                                            <h3 style="font-weight: 390; font-size:18px">${formatTitle(result.title)}</h3>
                                        </a>
                                        <p class="card-category">Catégorie : ${result.category_name ?? 'N/A'}</p>
                                        <p class="card-date">Publié le ${formatDate(result.date_publication)}</p>
                                        {{-- Modification ici: suppression de target="_blank" --}}
                                        <a href="${result.file_url}" class="btn btn-danger mt-auto">
                                            <i class="fas fa-download"></i> Télécharger
                                        </a>
                                    </div>
                                </div>
                            </div>`;
                        break;
                    case 'posts':
                        content = `
                            <div class="col-12 col-md-4 col-lg-3 mb-4 document-card">
                                <div class="card h-100">
                                    <a href="/articles/${result.slug}" class="text-decoration-none text-dark mb-3">
                                        <img src="{{ asset('storage') }}/${result.image}" class="card-img-top" alt="${result.title}" onerror="this.onerror=null;this.src='https://via.placeholder.com/400x250?text=No+Image';">
                                    </a>
                                    <div class="card-body">
                                        <a href="/articles/${result.slug}" class="text-decoration-none text-dark mb-3">
                                            <h3 style="font-weight: 390; font-size:18px">${formatTitle(result.title)}</h3>
                                        </a>
                                        <p class="card-date">Publié le ${formatDate(result.published_at)}</p>
                                    </div>
                                </div>
                            </div>`;
                        break;
                    case 'events':
                        content = `
                            <div class="col-12 col-md-4 col-lg-3 mb-4 document-card">
                                <div class="card h-100">
                                    <a href="/events/${result.slug}">
                                        <img src="{{ asset('storage') }}/${result.featured_image}" class="card-img-top" alt="${result.title}" onerror="this.onerror=null;this.src='https://via.placeholder.com/400x250?text=No+Image';">
                                    </a>
                                    <div class="card-body">
                                        <a href="/events/${result.slug}" class="text-decoration-none text-dark fw-light mb-3">
                                            <h3 style="font-weight: 390; font-size:18px">${formatTitle(result.title)}</h3>
                                        </a>
                                        <p class="card-date">Date : ${formatDate(result.start_date)} - ${formatDate(result.end_date)}</p>
                                    </div>
                                </div>
                            </div>`;
                        break;
                }
                resultatsContainer.innerHTML += content; // Ajoute la carte au conteneur des résultats
            });
            errorMessage.style.display = 'none'; // Cache le message d'erreur si des résultats sont affichés
        };

        /**
         * Rend les contrôles de pagination.
         * @param {object} pagination Les données de pagination reçues du serveur.
         */
        const renderPagination = (pagination) => {
            paginationContainer.innerHTML = '';
            const maxPagesToShow = 5; // Nombre maximal de boutons de pagination à afficher
            let startPage, endPage;

            if (pagination.totalPages <= maxPagesToShow) {
                // Moins de pages que le maximum, affiche toutes les pages
                startPage = 1;
                endPage = pagination.totalPages;
            } else {
                // Plus de pages que le maximum, calcule les pages à afficher
                startPage = Math.max(1, pagination.currentPage - Math.floor(maxPagesToShow / 2));
                endPage = Math.min(pagination.totalPages, startPage + maxPagesToShow - 1);

                // Ajuste startPage si endPage atteint la fin
                if (endPage === pagination.totalPages) {
                    startPage = Math.max(1, pagination.totalPages - maxPagesToShow + 1);
                }
            }

            // Bouton Précédent
            if (pagination.currentPage > 1) {
                paginationContainer.innerHTML += `<button class="btn btn-sm btn-outline-danger me-2" data-page="${pagination.currentPage - 1}">Précédent</button>`;
            }

            // Boutons des pages numériques
            for (let i = startPage; i <= endPage; i++) {
                const activeClass = i === pagination.currentPage ? 'btn-danger text-white' : 'btn-outline-danger';
                paginationContainer.innerHTML += `<button class="btn btn-sm ${activeClass} me-2" data-page="${i}">${i}</button>`;
            }

            // Bouton Suivant
            if (pagination.currentPage < pagination.totalPages) {
                paginationContainer.innerHTML += `<button class="btn btn-sm btn-outline-danger" data-page="${pagination.currentPage + 1}">Suivant</button>`;
            }

            // Ajoute les écouteurs d'événements aux boutons de pagination
            paginationContainer.querySelectorAll('button').forEach(button => {
                button.addEventListener('click', (event) => {
                    currentPage = parseInt(event.target.getAttribute('data-page'));
                    fetchResults(currentPage); // Relance la recherche pour la page sélectionnée
                });
            });
        };

        /**
         * Effectue la requête AJAX pour récupérer les résultats de recherche.
         * Gère l'affichage des résultats, des messages d'erreur et de la barre de progression.
         * @param {number} page Le numéro de la page à récupérer.
         */
        const fetchResults = async (page = 1) => {
            const query = searchInput.value.trim();
            const hasActiveFilters = Object.values(filters).some(arr => arr.length > 0);

            // Si la requête est vide ET qu'aucun filtre n'est sélectionné, masquer tout
            if (!query && !hasActiveFilters && page === 1) {
                resultatsContainer.innerHTML = '';
                errorMessage.style.display = 'none';
                paginationContainer.innerHTML = '';
                progressBarContainer.style.display = 'none';
                resultsCountElement.style.display = 'none';
                return; // Arrête la fonction
            }

            // Sinon, continuer la recherche et afficher les indicateurs de chargement
            resultatsContainer.innerHTML = '<p class="text-center">Chargement...</p>';
            errorMessage.style.display = 'none';
            progressBarContainer.style.display = 'block';
            progressBar.style.width = '0%';
            progressBar.classList.add('progress-bar-animated', 'progress-bar-striped');

            try {
                const response = await fetch("{{ route('search.results') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        query,
                        // Si aucun filtre de table n'est sélectionné, rechercher dans toutes les tables par défaut
                        tables: filters.tables.length ? filters.tables : ['posts', 'events', 'documents'],
                        months: filters.months.map(mois => moisStringEnNombre(mois)),
                        years: filters.years.map(Number),
                        page,
                        perPage: 12 // Nombre d'éléments par page
                    })
                });

                if (!response.ok) {
                    // Si la réponse n'est pas OK (par exemple, 500 Internal Server Error)
                    const errorText = await response.text();
                    console.error("Réponse du serveur non OK:", response.status, errorText);
                    throw new Error(`Erreur HTTP: ${response.status} - ${errorText.substring(0, 100)}...`);
                }

                const data = await response.json();
                resultatsContainer.innerHTML = ''; // Nettoyer les anciens résultats
                console.log('Réponse du serveur:', data); // Pour le débogage

                const allResults = [];
                // Ajouter les résultats de chaque type s'ils existent
                if (data.documents && data.documents.data) {
                    allResults.push(...data.documents.data.map(item => ({ ...item, type: 'documents' })));
                }
                if (data.posts && data.posts.data) {
                    allResults.push(...data.posts.data.map(item => ({ ...item, type: 'posts' })));
                }
                if (data.events && data.events.data) {
                    allResults.push(...data.events.data.map(item => ({ ...item, type: 'events' })));
                }

                if (allResults.length) {
                    // Tri combiné des résultats par date (du plus récent au plus ancien)
                    allResults.sort((a, b) => {
                        let dateA, dateB;
                        // Détermine la date pertinente pour chaque type de contenu
                        if (a.type === 'documents') dateA = new Date(a.date_publication);
                        else if (a.type === 'posts') dateA = new Date(a.published_at);
                        else if (a.type === 'events') dateA = new Date(a.start_date);

                        if (b.type === 'documents') dateB = new Date(b.date_publication);
                        else if (b.type === 'posts') dateB = new Date(b.published_at);
                        else if (b.type === 'events') dateB = new Date(b.start_date);

                        return dateB - dateA; // Trie par ordre décroissant de date
                    });

                    // Rend les éléments triés
                    allResults.forEach(item => renderItems([item], item.type));
                    resultsCountElement.textContent = `Nombre de fichiers trouvés : ${data.totalResults ?? allResults.length}`; // Utilise totalResults si disponible, sinon la longueur du tableau
                    resultsCountElement.style.display = 'block';
                    errorMessage.style.display = 'none';
                } else {
                    errorMessage.style.display = 'block'; // Affiche le message "Aucun résultat trouvé"
                    resultsCountElement.textContent = `Aucun fichier trouvé pour votre recherche.`;
                    resultsCountElement.style.display = 'block';
                    paginationContainer.innerHTML = ''; // Cache la pagination si aucun résultat
                }

                // Gère la pagination
                if (data.pagination) {
                    renderPagination(data.pagination);
                }

            } catch (error) {
                console.error("Erreur lors de la récupération des résultats:", error);
                resultatsContainer.innerHTML = '<p class="text-danger text-center">Une erreur est survenue lors de la recherche. Veuillez réessayer.</p>';
                errorMessage.style.display = 'none'; // Cache le message "aucun résultat" en cas d'erreur de réseau/serveur
                resultsCountElement.style.display = 'none'; // Cache le compteur en cas d'erreur
            } finally {
                // Finalise la barre de progression
                progressBar.style.width = '100%';
                progressBar.classList.remove('progress-bar-animated', 'progress-bar-striped');
                setTimeout(() => { progressBarContainer.style.display = 'none'; }, 500); // Cache après un court délai
            }
        };

        // --- Événements ---

        // Clic sur le bouton de recherche
        searchButton.addEventListener('click', () => {
            currentPage = 1; // Réinitialise la page à 1 pour une nouvelle recherche
            fetchResults(currentPage);
        });

        // Frappe dans la barre de recherche (avec délai pour éviter trop de requêtes)
        searchInput.addEventListener('keyup', () => {
            clearTimeout(typingTimer);
            const query = searchInput.value.trim();
            const hasActiveFilters = Object.values(filters).some(arr => arr.length > 0);

            if (!query && !hasActiveFilters) {
                // Si la barre de recherche est vide ET qu'aucun filtre n'est actif, effacer les résultats
                resultatsContainer.innerHTML = '';
                errorMessage.style.display = 'none';
                paginationContainer.innerHTML = '';
                resultsCountElement.style.display = 'none';
                progressBarContainer.style.display = 'none';
                return; // Sort de la fonction
            }

            // Sinon, déclencher la recherche après le délai
            typingTimer = setTimeout(() => {
                currentPage = 1; // Réinitialise la page à 1 lors de la saisie
                fetchResults(currentPage);
            }, typingDelay);
        });

        // Gestion des sélections dans les dropdowns de filtres
        dropdowns.forEach(dropdown => {
            dropdown.querySelectorAll('.dropdown-item').forEach(item => {
                item.addEventListener('click', (event) => {
                    event.preventDefault(); // Empêche le comportement par défaut du lien
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    const value = item.getAttribute('data-value');
                    // Récupère le type de filtre (tables, months, years) à partir de l'ID du menu déroulant
                    const dropdownName = item.closest('.dropdown-menu').id.split('-')[0];

                    // Inverse l'état de la checkbox
                    checkbox.checked = !checkbox.checked;

                    // Ajoute ou supprime la valeur du filtre
                    if (checkbox.checked) {
                        if (!filters[dropdownName].includes(value)) {
                            filters[dropdownName].push(value);
                        }
                    } else {
                        filters[dropdownName] = filters[dropdownName].filter(v => v !== value);
                    }

                    updateSelectedItems(); // Met à jour l'affichage des filtres sélectionnés
                    currentPage = 1; // Réinitialise la page à 1 lors d'un changement de filtre
                    fetchResults(currentPage); // Relance la recherche
                });
            });
        });

        // --- Initialisation ---

        // Ne rien afficher au chargement initial
        resultatsContainer.innerHTML = '';
        errorMessage.style.display = 'none';
        paginationContainer.innerHTML = '';
        resultsCountElement.style.display = 'none'; // Cache le compteur au départ
        progressBarContainer.style.display = 'none'; // Cache la barre de progression au départ

        // Met à jour l'affichage des filtres au chargement (au cas où il y en aurait des prédéfinis)
        updateSelectedItems();
    });
</script>
@endsection