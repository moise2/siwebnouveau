@extends('frontend.layouts.page', [
    'pageTitle' => $title,
    'pageId' => 'articles',
])

@section('page-content')
<h2 class="card-title">{{ $title }}</h2>

<div class="search-input-group mb-3 d-flex">
    <input id="search-input" class="form-control search_input" type="text" placeholder="Rechercher des documents...">
    <button id="search-button" class="btn btn-danger search_icon" type="button">
        <i class="fas fa-search"></i>
    </button>
</div>

<div id="search-progress" class="progress mb-3" style="height: 6px; display: none;">
    <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 100%"></div>
</div>

<div id="spinner-loader" class="text-center my-4" style="display: none;">
    <div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Chargement...</span>
    </div>
</div>

<div id="selectedFilters" class="d-flex flex-wrap gap-2 mb-3"></div>

<div class="dropdown-container mt-4 d-flex flex-wrap gap-2">
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButtonCategorie" data-bs-toggle="dropdown" aria-expanded="false" {{ $autoSelectedCount > 0 ? 'disabled' : '' }}>
            Catégories <span id="categorie-badge" class="badge bg-danger ms-2">{{ $autoSelectedCount }}</span>
        </button>
        <div id="categorie-dropdown" class="dropdown-menu">
            @foreach($allCategories as $category)
                <a class="dropdown-item {{ in_array($category->id, $autoSelectedCategories) ? 'disabled' : '' }}" href="#" data-value="{{ $category->id }}">
                    <input type="checkbox" class="me-2" {{ in_array($category->id, $autoSelectedCategories) ? 'checked disabled' : '' }} /> {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButtonMois" data-bs-toggle="dropdown" aria-expanded="false">
            Mois <span id="mois-badge" class="badge bg-danger ms-2">0</span>
        </button>
        <div id="mois-dropdown" class="dropdown-menu">
            @php
                $months = [
                    1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                    5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                    9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                ];
            @endphp
            @foreach($months as $num => $name)
                <a class="dropdown-item" href="#" data-value="{{ $num }}">
                    <input type="checkbox" class="me-2" /> {{ $name }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButtonAnnee" data-bs-toggle="dropdown" aria-expanded="false">
            Années <span id="annee-badge" class="badge bg-danger ms-2">0</span>
        </button>
        <div id="annee-dropdown" class="dropdown-menu">
            @php $currentYear = date('Y'); @endphp
            @for($year = $currentYear; $year >= 2000; $year--)
                <a class="dropdown-item" href="#" data-value="{{ $year }}">
                    <input type="checkbox" class="me-2" /> {{ $year }}
                </a>
            @endfor
        </div>
    </div>

    {{-- Added the missing Reset Filters Button --}}
    <button id="reset-filters" class="btn btn-outline-secondary" type="button" style="display: none;">
        Réinitialiser les filtres <i class="fas fa-undo"></i>
    </button>
</div>

<div id="no-results-message" class="text-danger text-center fw-bold mt-3" style="display: none;">
    Aucun résultat trouvé !
</div>

<div id="global-search-button-container" class="text-center mt-2" style="display: none;">
    <a href="{{ route('search.index') }}" class="btn btn-outline-danger">
        Effectuer une recherche globale
    </a>
</div>

<div class="table-container mt-4">
    <div class="row" id="resultats">
        @include('frontend.partials.documents_list', ['documents' => $documents, 'routes' => $routes])
    </div>

    <div id="pagination-container" class="d-flex justify-content-center mt-4">
        {{ $documents->links('pagination::bootstrap-4') }}
    </div>

    <p id="documents_count" class="text-center mt-3">{{ $documents->count() }} document(s) affiché(s).</p>
    <p class="text-center">Total des documents : <span id="total_documents">{{ $documents->total() }}</span></p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Selection des éléments du DOM
    const searchInput = document.querySelector('#search-input');
    const searchButton = document.querySelector('#search-button');
    const resultsContainer = document.querySelector('#resultats');
    const noResultsMessage = document.querySelector('#no-results-message');
    const paginationContainer = document.querySelector('#pagination-container');
    const documentsCountElement = document.querySelector('#documents_count');
    const totalDocumentsElement = document.querySelector('#total_documents');
    const selectedFiltersContainer = document.querySelector('#selectedFilters');
    const categorieBadge = document.querySelector('#categorie-badge');
    const moisBadge = document.querySelector('#mois-badge');
    const anneeBadge = document.querySelector('#annee-badge');
    const globalSearchButton = document.querySelector('#global-search-button-container');
    const progressBar = document.querySelector('#search-progress');
    const spinner = document.querySelector('#spinner-loader');
    const resetFiltersButton = document.querySelector('#reset-filters');

    // Mappage des numéros de mois aux noms pour l'affichage des badges (chips)
    const monthNumberToName = {
        1: 'Janvier', 2: 'Février', 3: 'Mars', 4: 'Avril',
        5: 'Mai', 6: 'Juin', 7: 'Juillet', 8: 'Août',
        9: 'Septembre', 10: 'Octobre', 11: 'Novembre', 12: 'Décembre'
    };

    // Tableaux pour stocker les IDs/valeurs des filtres sélectionnés.
    // Initialisation avec les catégories auto-sélectionnées
    let selectedCategories = @json($autoSelectedCategories);
    let selectedMonths = [];
    let selectedYears = [];

    // Stocke les catégories initialement auto-sélectionnées (non-supprimables)
    const initialAutoSelectedCategories = [...selectedCategories];

    // Retrieve routes passed by Blade to JavaScript
    const jsRoutes = @json($jsRoutes ?? []);
    // Also include allCategories for category names in JS for badges
    const allCategoriesJs = @json($allCategories->keyBy('id'));

    // Store the initial total documents count when the page loads
    if (totalDocumentsElement) {
        totalDocumentsElement.textContent = {{ $documents->total() }};
    }

    /**
     * Updates the selected filter "chips" (badges) and the counters in the dropdowns.
     * This function is called whenever a filter is modified.
     */
    function updateSelectedFilters() {
        console.log('--- updateSelectedFilters appelée ---');
        if (!selectedFiltersContainer) {
            console.error("selectedFiltersContainer not found!");
            return;
        }
        selectedFiltersContainer.innerHTML = ''; // Clear previously displayed filters

        // Display auto-selected categories (they are not interactive for removal)
        initialAutoSelectedCategories.forEach(catId => {
            const cat = allCategoriesJs[catId];
            if (cat) {
                const span = document.createElement('span');
                span.className = 'badge bg-secondary text-white me-2'; // Use a different style for non-removable filters
                span.textContent = cat.name;
                selectedFiltersContainer.appendChild(span);
            }
        });

        // Display dynamically selected categories (removable)
        selectedCategories.filter(catId => !initialAutoSelectedCategories.includes(catId)).forEach(catId => {
            const cat = allCategoriesJs[catId];
            if (cat) {
                const span = document.createElement('span');
                span.className = 'badge bg-danger text-white me-2';
                span.innerHTML = `${cat.name} <i class="fas fa-times-circle remove-filter" data-filter-type="category" data-filter-value="${catId}" style="cursor: pointer;"></i>`;
                selectedFiltersContainer.appendChild(span);
            }
        });

        // Display selected months
        selectedMonths.forEach(monthNum => {
            const monthName = monthNumberToName[monthNum];
            if (monthName) {
                const span = document.createElement('span');
                span.className = 'badge bg-danger text-white me-2';
                span.innerHTML = `${monthName} <i class="fas fa-times-circle remove-filter" data-filter-type="month" data-filter-value="${monthNum}" style="cursor: pointer;"></i>`;
                selectedFiltersContainer.appendChild(span);
            }
        });

        // Display selected years
        selectedYears.forEach(year => {
            const span = document.createElement('span');
            span.className = 'badge bg-danger text-white me-2';
            span.innerHTML = `${year} <i class="fas fa-times-circle remove-filter" data-filter-type="year" data-filter-value="${year}" style="cursor: pointer;"></i>`;
            selectedFiltersContainer.appendChild(span);
        });

        // Update counters in dropdown button badges
        if (categorieBadge) {
            categorieBadge.textContent = selectedCategories.length > 0 ? selectedCategories.length : '0';
        }
        if (moisBadge) {
            moisBadge.textContent = selectedMonths.length > 0 ? selectedMonths.length : '0';
        }
        if (anneeBadge) {
            anneeBadge.textContent = selectedYears.length > 0 ? selectedYears.length : '0';
        }

        // Show/hide reset filters button
        if (resetFiltersButton) {
            const hasActiveFilters = searchInput.value.trim() !== '' || selectedMonths.length > 0 || selectedYears.length > 0 || selectedCategories.length > initialAutoSelectedCategories.length;
            resetFiltersButton.style.display = hasActiveFilters ? 'inline-block' : 'none';
        }

        // Handle filter removal when the cross icon is clicked.
        selectedFiltersContainer.querySelectorAll('.remove-filter').forEach(icon => {
            icon.addEventListener('click', (e) => {
                const type = e.target.dataset.filterType;
                const value = parseInt(e.target.dataset.filterValue);

                if (type === 'category') {
                    selectedCategories = selectedCategories.filter(c => c !== value);
                    const checkbox = document.querySelector(`#categorie-dropdown a[data-value="${value}"] input[type="checkbox"]`);
                    if (checkbox) checkbox.checked = false;
                } else if (type === 'month') {
                    selectedMonths = selectedMonths.filter(m => m !== value);
                    const checkbox = document.querySelector(`#mois-dropdown a[data-value="${value}"] input[type="checkbox"]`);
                    if (checkbox) checkbox.checked = false;
                } else if (type === 'year') {
                    selectedYears = selectedYears.filter(y => y !== value);
                    const checkbox = document.querySelector(`#annee-dropdown a[data-value="${value}"] input[type="checkbox"]`);
                    if (checkbox) checkbox.checked = false;
                }
                updateSelectedFilters();
                fetchDocuments();
            });
        });
    }

    /**
     * Configures dropdowns (months, years, categories) to manage checkbox selections.
     * @param {string} dropdownId The ID of the dropdown container (e.g., 'mois-dropdown').
     * @param {Array} selectedArray The JavaScript array to store selected values (e.g., selectedMonths).
     * @param {boolean} isCategoryDropdown Indicates if this is the category dropdown (special handling for disabled items)
     */
    function setupDropdown(dropdownId, selectedArray, isCategoryDropdown = false) {
        const dropdown = document.getElementById(dropdownId);
        if (!dropdown) {
            console.warn(`Dropdown with ID ${dropdownId} not found.`);
            return;
        }

        dropdown.querySelectorAll('.dropdown-item').forEach(item => {
            const checkbox = item.querySelector('input[type="checkbox"]');
            const value = parseInt(item.getAttribute('data-value')); // Parse value to integer

            if (checkbox && !checkbox.disabled) { // Only add event listener if checkbox is not disabled
                item.addEventListener('click', (e) => {
                    e.preventDefault(); // Prevent default link behavior
                    checkbox.checked = !checkbox.checked;

                    if (checkbox.checked) {
                        if (!selectedArray.includes(value)) {
                            selectedArray.push(value);
                        }
                    } else {
                        const index = selectedArray.indexOf(value);
                        if (index !== -1) {
                            selectedArray.splice(index, 1);
                        }
                    }
                    updateSelectedFilters();
                    fetchDocuments();
                });
            }
        });
    }

    /**
     * Updates the "checked" state of checkboxes in dropdowns
     * based on the values stored in selectedMonths and selectedYears.
     */
    function updateDropdownCheckboxes() {
        document.querySelectorAll('.dropdown-menu .dropdown-item input[type="checkbox"]').forEach(checkbox => {
            const parentItem = checkbox.closest('.dropdown-item');
            if (!parentItem) {
                console.warn("Could not find parent .dropdown-item for a checkbox.");
                return;
            }

            const value = parseInt(parentItem.getAttribute('data-value'));

            if (parentItem.closest('#categorie-dropdown')) {
                // For categories, only update if not initially disabled by Blade
                if (!checkbox.disabled) {
                    checkbox.checked = selectedCategories.includes(value);
                }
            } else if (parentItem.closest('#mois-dropdown')) {
                checkbox.checked = selectedMonths.includes(value);
            } else if (parentItem.closest('#annee-dropdown')) {
                checkbox.checked = selectedYears.includes(value);
            }
        });
    }

    /**
     * Determines the Font Awesome icon class based on the file extension.
     * @param {string} filePath The file path or URL.
     * @returns {string} The Font Awesome icon CSS class.
     */
    function getFileIconClass(filePath) {
        const extension = filePath.split('.').pop().toLowerCase();
        switch (extension) {
            case 'pdf': return 'fa-file-pdf';
            case 'doc': case 'docx': return 'fa-file-word';
            case 'xls': case 'xlsx': return 'fa-file-excel';
            case 'ppt': case 'pptx': return 'fa-file-powerpoint';
            case 'zip': case 'rar': case '7z': return 'fa-file-archive';
            case 'jpg': case 'jpeg': case 'png': case 'gif': case 'webp': return 'fa-file-image';
            case 'txt': return 'fa-file-alt';
            default: return 'fa-file';
        }
    }

    /**
     * Formats a date string into a localized format.
     * @param {string} dateStr The date string.
     * @returns {string} The formatted date.
     */
    const formatDate = dateStr => {
        if (!dateStr) return "Date non disponible";
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateStr).toLocaleDateString('fr-FR', options);
    };

    /**
     * Displays documents in the results container.
     * This function directly takes the array of documents (data.documents.data)
     * and generates the HTML for each document card.
     * @param {Array} documents Array of documents to display.
     * @param {Object} routes Object containing routes to generate show and download URLs.
     */
    function renderDocuments(documents, routes) {
        if (!resultsContainer) {
            console.error("resultsContainer not found for rendering documents!");
            return;
        }
        resultsContainer.innerHTML = documents.map(doc => {
            const iconClass = getFileIconClass(doc.file_url || '');

            const showUrl = routes.show ? routes.show.replace('PLACEHOLDER_SLUG', doc.slug) : '#'; // Changed to slug
            const downloadUrl = routes.download ? routes.download.replace('PLACEHOLDER_ID', doc.id) : '#'; // Changed to ID

            return `
            <div class="col-12 col-md-4 col-lg-3 mb-4 document-card">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column">
                        <a href="${showUrl}" target="_blank"
                           class="text-decoration-none text-dark d-flex align-items-center gap-2 mb-3">
                            <i class="fas ${iconClass} fa-2x text-danger" style="font-size: 1rem"></i>
                            <h3 class="card-title mb-0" style="font-size: 0.9rem">${ucfirst(doc.title || "Titre non disponible")}</h3>
                        </a>

                        <p class="download-count text-muted mb-2">
                            <i class="fas fa-file-download" style="font-size: 0.8rem"></i> ${doc.download_count || 0} téléchargements
                        </p>

                        <p class="card-category text-secondary mb-1">Catégories :
                            <span class="text-danger">${doc.category || "Aucune catégorie"}</span>
                        </p>

                        <p class="card-date text-muted mb-4">
                            <strong>Publié le ${formatDate(doc.date_publication)}</strong>
                        </p>

                        <a href="${downloadUrl}" target="_blank"
                           class="btn btn-danger mt-auto d-flex align-items-center justify-content-center gap-1"
                           style="padding: 4px 8px; font-size: 0.875rem;">
                            <i class="fas fa-download"></i> Télécharger
                        </a>
                    </div>
                </div>
            </div>
            `;
        }).join('');
    }

    /**
     * Main function to send the AJAX request to the Laravel controller
     * and update the user interface with the results.
     * @param {number} page The page number to retrieve (defaults to 1).
     */
    function fetchDocuments(page = 1) {
        const query = searchInput.value.trim();
        const filters = {
            categories: selectedCategories,
            months: selectedMonths,
            years: selectedYears
        };

        // Ensure these protections are in place and elements exist
        if (progressBar) progressBar.style.width = '0%';
        if (progressBar) progressBar.style.display = 'block';
        if (spinner) spinner.style.display = 'block';
        if (noResultsMessage) noResultsMessage.style.display = 'none';
        if (globalSearchButton) globalSearchButton.style.display = 'none';

        if (resultsContainer) resultsContainer.innerHTML = ''; // Clear previous results
        if (paginationContainer) paginationContainer.innerHTML = ''; // Clear previous pagination
        // Crucial protection here!
        if (documentsCountElement) {
            documentsCountElement.textContent = `0 document(s) affiché(s).`;
        }


        if (!jsRoutes.search) {
            console.error("The search route (jsRoutes.search) is not defined. Make sure to pass it from the controller.");
            if (spinner) spinner.style.display = 'none';
            if (progressBar) progressBar.style.display = 'none';
            if (noResultsMessage) {
                noResultsMessage.textContent = "Erreur: la route de recherche n'est pas configurée.";
                noResultsMessage.style.display = 'block';
            }
            return;
        }

        fetch(`${jsRoutes.search}?page=${page}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                query: query,
                filters: filters,
                page: page
            })
        })
        .then(response => {
            if (progressBar) progressBar.style.width = '100%';
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            // Hide loading indicators
            if (spinner) spinner.style.display = 'none';
            if (progressBar) progressBar.style.display = 'none';

            let currentDisplayedCount = 0; // Initialize a variable to hold the current page's count

            if (data.html && resultsContainer) { // If controller directly returns HTML for documents
                resultsContainer.innerHTML = data.html;
                if (noResultsMessage) noResultsMessage.style.display = 'none';
                if (globalSearchButton) globalSearchButton.style.display = 'none';
                // Attempt to count documents if HTML is directly rendered
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data.html;
                currentDisplayedCount = tempDiv.querySelectorAll('.document-card').length;

            } else if (data.documents && data.documents.data && resultsContainer) { // If controller returns a paginator object
                renderDocuments(data.documents.data, jsRoutes);
                if (noResultsMessage) noResultsMessage.style.display = 'none';
                if (globalSearchButton) globalSearchButton.style.display = 'none';
                currentDisplayedCount = data.documents.data.length; // Get the actual number of documents on this page
            } else {
                if (resultsContainer) resultsContainer.innerHTML = "";
                if (noResultsMessage) noResultsMessage.style.display = 'block';
                if (globalSearchButton) globalSearchButton.style.display = 'block';
                currentDisplayedCount = 0; // No documents found or data structure is unexpected
            }

            // Update pagination
            if (paginationContainer) {
                if (data.pagination) {
                    paginationContainer.innerHTML = data.pagination;
                } else if (data.pagination_html) {
                    paginationContainer.innerHTML = data.pagination_html;
                } else {
                    paginationContainer.innerHTML = '';
                }
            }

            // Crucial protection here too!
            if (documentsCountElement) {
                documentsCountElement.textContent = `${currentDisplayedCount} document(s) affiché(s).`;
            }
            // totalDocumentsElement is intentionally NOT updated here as per your request

            updateDropdownCheckboxes(); // Ensure checkboxes reflect filters
            updateSelectedFilters(); // Update filter "chips"

        })
        .catch(error => {
            console.error('Erreur lors de la récupération des documents :', error);
            if (spinner) spinner.style.display = 'none';
            if (progressBar) progressBar.style.display = 'none';
            if (noResultsMessage) {
                noResultsMessage.textContent = "Une erreur est survenue lors du chargement des documents. Veuillez réessayer.";
                noResultsMessage.style.display = 'block';
            }
            if (globalSearchButton) globalSearchButton.style.display = 'block';
            if (resultsContainer) resultsContainer.innerHTML = "";
            if (paginationContainer) paginationContainer.innerHTML = "";
            // And here as well!
            if (documentsCountElement) {
                documentsCountElement.textContent = `0 document(s) affiché(s).`;
            }
            // totalDocumentsElement remains untouched on error
        });
    }

    // --- Initialisation des fonctionnalités au chargement de la page ---

    // Configure the dropdowns for months, years, and categories.
    setupDropdown('mois-dropdown', selectedMonths);
    setupDropdown('annee-dropdown', selectedYears);
    setupDropdown('categorie-dropdown', selectedCategories, true); // Pass true for isCategoryDropdown

    // Attach event listeners
    let typingTimer;
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                fetchDocuments();
            }, 500); // 500ms delay
        });
    } else {
        console.warn("Search input element not found.");
    }


    if (searchButton) {
        searchButton.addEventListener('click', () => {
            clearTimeout(typingTimer);
            fetchDocuments();
        });
    } else {
        console.warn("Search button element not found.");
    }


    // Handle pagination link clicks (event delegation)
    if (paginationContainer) {
        paginationContainer.addEventListener('click', function(event) {
            if (event.target.tagName === 'A' && event.target.href) {
                event.preventDefault();
                const url = new URL(event.target.href);
                const page = url.searchParams.get('page');
                if (page) {
                    fetchDocuments(parseInt(page));
                }
            }
        });
    } else {
        console.warn("Pagination container element not found.");
    }


    // Reset filters button
    if (resetFiltersButton) {
        resetFiltersButton.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            selectedMonths = [];
            selectedYears = [];
            // Reset only dynamically selected categories, keep auto-selected ones
            selectedCategories = [...initialAutoSelectedCategories];

            // Reset checkbox states in dropdowns (except auto-selected/disabled categories)
            document.querySelectorAll('#mois-dropdown input[type="checkbox"]').forEach(cb => cb.checked = false);
            document.querySelectorAll('#annee-dropdown input[type="checkbox"]').forEach(cb => cb.checked = false);
            // Also reset category checkboxes that are not disabled
            document.querySelectorAll('#categorie-dropdown input[type="checkbox"]').forEach(cb => {
                if (!cb.disabled) {
                    cb.checked = false;
                }
            });

            // CRUCIAL: Update checkboxes and filters visual state after reset
            updateDropdownCheckboxes();
            updateSelectedFilters();
            fetchDocuments(); // Trigger a new search with reset filters
        });
    } else {
        console.warn("Reset filters button element not found.");
    }


    // Initial calls to display default filters and documents on page load.
    updateDropdownCheckboxes(); // To set initial checkbox states based on selectedCategories
    updateSelectedFilters(); // To display initial filter badges
    // fetchDocuments(); // Uncomment if you want AJAX search to trigger on initial load, otherwise Blade renders initial documents
});

// Utility function to capitalize the first letter of a string.
function ucfirst(str) {
    if (typeof str !== 'string' || str.length === 0) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

</script>
@endsection