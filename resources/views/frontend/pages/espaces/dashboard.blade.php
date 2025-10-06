<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord PTF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Hamburgers.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/hamburgers/1.1.3/hamburgers.min.css">

    <style>
        /* Définition de la largeur de la sidebar via une variable CSS */
        :root {
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            /* Transition pour le margin-left du body pour les écrans larges */
            transition: margin-left 0.3s ease-in-out;
            overflow-x: hidden; /* Empêche le défilement horizontal global */
            scroll-behavior: smooth; /* Défilement doux pour les ancres */
        }

        /* Classe pour désactiver le défilement du body lorsque la sidebar est ouverte sur mobile */
        body.sidebar-open-mobile {
            overflow: hidden;
        }

        .sidebar-nav {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            /* Par défaut, masqué sur mobile (hors écran) */
            left: calc(-1 * var(--sidebar-width)); 
            background: linear-gradient(to bottom, #1a72a9, #0f4d7a);
            color: #ecf0f1;
            padding-top: 20px;
            transition: left 0.3s ease-in-out; /* Animation pour l'ouverture/fermeture */
            overflow-y: auto; /* Permet le défilement si le contenu est trop long */
            z-index: 1030; /* Assure que la sidebar est au-dessus du contenu principal */
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        /* Quand la sidebar est active (ouverte sur mobile), elle se positionne à gauche */
        .sidebar-nav.active {
            left: 0;
        }

        .sidebar-header {
            padding: 15px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 60px;
        }

        .nav-link {
            color: #ecf0f1;
            display: flex;
            align-items: center;
            padding: 12px 20px;
            margin: 0 10px 5px 10px; /* Ajout du margin selon votre inspiration */
            border-radius: 5px;
            transition: background-color 0.2s, color 0.2s, border-bottom 0.2s;
            position: relative;
            overflow: hidden;
            text-decoration: none; /* Assurez-vous que les liens n'ont pas de soulignement par défaut */
        }

        .nav-link:hover, .nav-link.active {
            background: linear-gradient(to right,rgb(125, 2, 2),rgb(1, 97, 123));
            color: #fff;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 3px;
            background-color: transparent;
            transition: background-color 0.2s ease-in-out;
        }

        .nav-link:hover::after, .nav-link.active::after {
            background-color: #dc3545;
        }

        .nav-link i {
            margin-right: 15px;
            font-size: 22px;
            min-width: 25px;
            text-align: center;
        }

        .nav-link span {
            white-space: nowrap;
            opacity: 1;
            transition: opacity 0.3s ease-in-out, margin-left 0.3s ease-in-out;
            display: block;
        }

        /* Styles for submenu items */
        .sidebar-nav .components ul {
            padding-left: 0; /* Remove default ul padding */
            list-style: none;
        }

        .sidebar-nav .components ul li {
            padding-left: 0; /* Changed from 30px to 0 for parent list items */
        }
        
        /* Specific padding for sub-items */
        .sidebar-nav .components .nav-link.sub-item {
            padding: 8px 20px 8px 45px; /* Added 45px left padding for indentation */
            font-size: 0.9em;
            margin: 0 10px 2px 10px;
        }

        .sidebar-nav .components .nav-link.sub-item:hover,
        .sidebar-nav .components .nav-link.sub-item.active {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .sidebar-nav.collapsed .nav-link span {
            opacity: 0;
            width: 0;
            overflow: hidden;
            margin-left: -15px;
        }

        .main-content {
            padding: 30px;
            width: 100%;
            min-height: 100vh;
            padding-top: 80px; 
        }

        /* Bouton Hamburger - Position fixe pour être toujours accessible */
        #hamburgerToggle {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1032;
            background: none;
            border: none;
            cursor: pointer;
            padding: 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            width: 35px;
            height: 30px;
            transition: all 0.3s ease-in-out;
        }
        #hamburgerToggle .bar {
            display: block;
            width: 100%;
            height: 4px;
            background-color: #1a72a9;
            border-radius: 5px;
            transition: all 0.3s ease-in-out;
        }
        /* Styles pour l'animation du hamburger en X */
        #hamburgerToggle.is-active .hamburger-inner {
            background-color: transparent !important;
        }

        #hamburgerToggle.is-active .hamburger-inner::before {
            transform: translateY(10px) rotate(45deg);
        }

        #hamburgerToggle.is-active .hamburger-inner::after {
            transform: translateY(-10px) rotate(-45deg);
        }


        /* Overlay pour fermer la sidebar en cliquant à l'extérieur (mobile) */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1029;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        .overlay.active {
            display: block;
            opacity: 1;
        }

        /* Media query pour les écrans larges (desktop) */
        @media (min-width: 992px) {
            .sidebar-nav {
                left: 0;
            }
            body {
                margin-left: var(--sidebar-width);
            }
            #hamburgerToggle {
                display: none;
            }
            .overlay {
                display: none !important;
            }
        }

        /* General dashboard card styles */
        .dashboard-card {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            margin-bottom: 25px;
            transition: transform 0.2s ease-in-out;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .progress {
            height: 10px;
            border-radius: 5px;
            background-color: #e9ecef;
        }

        .progress-bar {
            background-color: #28a745;
        }

        /* Styling for the program/action/activity tree view */
        .programme-item, .action-item, .activite-item {
            padding: 5px 0;
        }

        .programme-item > .d-flex {
            cursor: pointer;
            padding: 8px 0;
            font-size: 1.1em;
            color: #333;
            align-items: center;
        }
        
        .programme-item > .d-flex:hover {
            color: #007bff;
        }

        .action-item {
            border-left: 3px solid #ced4da;
            margin-left: 20px;
            padding-left: 15px;
            margin-top: 8px;
            margin-bottom: 8px;
        }

        .action-item .d-flex {
            font-size: 1em;
            color: #555;
            padding: 5px 0;
            align-items: center;
        }

        .activite-item {
            margin: 5px 0;
            margin-left: 20px;
            padding: 3px 0;
        }

        .activite-item .btn-link {
            font-size: 0.95em;
            padding: 0;
            text-align: left;
            color: #007bff;
            text-decoration: none;
        }

        .activite-item .btn-link:hover {
            text-decoration: underline;
        }

        .collapse.show {
            display: block;
        }
        .collapse:not(.show) {
            display: none;
        }
        .collapsing {
            height: 0;
            overflow: hidden;
            transition: height 0.35s ease;
        }

        .modal-header {
            background-color: #007bff;
            color: white;
            border-bottom: none;
        }

        .modal-title {
            color: white;
        }

        .modal-footer {
            border-top: none;
        }

        .card-header {
            background-color: #e9ecef;
            border-bottom: 1px solid #dee2e6;
            font-weight: bold;
            padding: 1rem 1.5rem;
        }

        .table thead th {
            background-color: #e9ecef;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .badge {
            padding: 0.5em 0.8em;
            border-radius: 0.5rem;
            font-weight: 600;
        }
        
        .card.text-white h5, .card.text-white h2 {
            color: #fff !important;
        }

        /* Ensure charts take up available width and height but do not stretch */
        .chart-container {
            max-width: 100%;
            height: auto; 
            max-height: 350px; 
            margin-bottom: 20px;
        }
        .chart-container canvas {
            max-width: 100%;
            height: auto;
            max-height: 350px;
        }


        /* Blinking button */
        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7);
            }
            70% {
                transform: scale(1.05);
                box-shadow: 0 0 0 15px rgba(0, 123, 255, 0);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
            }
        }

        .return-home-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1050; /* Above modals */
            animation: pulse 2s infinite;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #007bff;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-decoration: none;
            font-size: 1.5rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .return-home-button:hover {
            background-color: #0056b3;
            animation: none; /* Stop pulsing on hover */
            transform: scale(1.1);
            color: white; /* Ensure text color remains white on hover */
        }

        /* Spinner Styles */
        .spinner-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2000; /* Higher than other elements */
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
        }

        .spinner-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .spinner-border-lg {
            width: 3rem;
            height: 3rem;
            border-width: 0.3em;
        }

        /* Added for discrete filtering */
        #programs-table-body {
            min-height: 100px; /* Adjust as needed to prevent height collapse */
        }
    </style>
</head>
<body>

    <!-- Le bouton hamburger est maintenant en dehors de la sidebar pour être toujours visible sur mobile -->
    <button class="hamburger hamburger--spin" type="button" id="hamburgerToggle" aria-label="Ouvrir le menu" aria-controls="navigation" aria-expanded="false">
        <span class="hamburger-box">
            <span class="hamburger-inner"></span>
        </span>
    </button>

    <div class="sidebar-nav" id="sidebar-wrapper">
        <div class="sidebar-header">
            <!-- Le bouton hamburger a été déplacé à l'extérieur de cette div -->
        </div>
        <ul class="list-unstyled components">
            <li>
                <a href="#dashboard-cards" class="nav-link active">
                    <i class='bx bx-home'></i>
                    <span>Accueil</span>
                </a>
            </li>
            <li>
                <a href="#programme-tree-card" class="nav-link">
                    <i class='bx bx-sitemap'></i>
                    <span>Programmes</span>
                </a>
            </li>
            <li>
                <a href="#programs-tables-section" class="nav-link">
                    <i class='bx bx-network-chart'></i>
                    <span>Tableaux des Programmes</span>
                </a>
            </li>
            <li>
                <a href="#charts-card" class="nav-link">
                    <i class='bx bx-chart'></i>
                    <span>Graphiques</span>
                </a>
            </li>
            <li>
                {{-- Formulaire de déconnexion pour Laravel --}}
                <form id="logout-form" action="{{ route('logout.dashboard') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class='bx bx-exit'></i>
                    <span>Déconnexion</span>
                </a>
            </li>
            
        </ul>
    </div>
    
    <!-- Overlay for mobile sidebar -->
    <div class="overlay" id="sidebarOverlay"></div>

    <div class="main-content" id="page-content-wrapper">
        <div class="container-fluid pt-5">
            <h2 class="mb-4 text-primary">Tableau de Bord PTF</h2>
            <hr class="bg-primary my-3">
            <!-- This part will be rendered by Laravel with the actual user name -->
            <p class="text-muted mb-0">
                Bienvenue
                @if(Auth::check())
                {{ Auth::user()->name }}
                @else
                    Visiteur
                @endif
            </p>
            <hr class="bg-secondary my-3">

            <!-- Filter and Search Section -->
            <div class="row g-4 mb-4 align-items-center">
                <div class="col-md-4">
                    <label for="yearFilter" class="form-label">Filtrer par année d'exercice:</label>
                    <select class="form-select" id="yearFilter">
                        <!-- Years will be populated by JS -->
                    </select>
                </div>
                <div class="col-md-8">
                    <label for="programSearch" class="form-label">Rechercher programme/activité:</label>
                    <input type="text" class="form-control" id="programSearch" placeholder="Rechercher...">
                </div>
            </div>

            <div class="row g-4" id="dashboard-cards">
                <div class="col-md-4">
                    <div class="card text-white shadow-sm border-0" style="background: linear-gradient(135deg, #007bff, #0056b3); border-radius: 1rem;">
                        <div class="card-body text-center py-4">
                            <h5 class="card-title fw-semibold">Programmes Actifs</h5>
                            <h2 class="display-5 mt-3">0</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-white shadow-sm border-0" style="background: linear-gradient(135deg, #28a745, #1e7e34); border-radius: 1rem;">
                        <div class="card-body text-center py-4">
                            <h5 class="card-title fw-semibold"> Actions en cours</h5>
                            <h2 class="display-5 mt-3">0</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-white shadow-sm border-0" style="background: linear-gradient(135deg, #17a2b8, #117a8b); border-radius: 1rem;">
                        <div class="card-body text-center py-4">
                            <h5 class="card-title fw-semibold">Activités planifiées</h5>
                            <h2 class="display-5 mt-3">0</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-4" id="programme-tree-card">
                <div class="card dashboard-card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Vue détaillée des programmes</h5>
                    </div>
                    <div class="card-body" id="programme-tree">
                        <!-- Tree view content will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- New sections for program status tables (unified as requested) -->
            <div id="programs-tables-section">
                <div class="card dashboard-card mt-4" id="programs-list-card">
                    <div class="card-header">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                            <h5 class="mb-0 mb-2 mb-md-0">Liste des Programmes</h5>
                            <div class="col-md-4 mt-3 mt-md-0">
                                <label for="etatFilter" class="form-label fw-bold">Filtrer par état :</label>
                                <select id="etatFilter" class="form-select" multiple>
                                    <option value="all" selected>Tous</option>
                                    <option value="not_started">Non démarré</option>
                                    <!-- 'Démarré' est combiné avec 'En cours' -->
                                    <option value="in_progress">En cours</option>
                                    <option value="completed">Terminé</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Programme</th>
                                        <th translate="no">Consulter</th> <!-- Changed as requested and added translate="no" -->
                                        <th>Progression physique</th>
                                        <th>État</th>
                                    </tr>
                                </thead>
                                <tbody id="programs-table-body">
                                    <!-- Content will be loaded dynamically by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-12 mt-4" id="charts-card">
                <div class="card dashboard-card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Analyses et Graphiques</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <!-- Controls for the synthesis chart -->
                            <div class="col-12 mb-4">
                                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
                                    <div class="flex-grow-1 me-md-3 mb-3 mb-md-0">
                                        <label for="programSelectForSynthesis" class="form-label me-3 mb-0">Sélectionner des programmes pour la synthèse:</label>
                                        <select class="form-select" id="programSelectForSynthesis" multiple aria-label="Sélectionner des programmes" style="min-width: 250px;">
                                            <!-- Options will be populated by JS -->
                                        </select>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="toggleSynthesisChart" checked>
                                        <label class="form-check-label" for="toggleSynthesisChart">Afficher la synthèse globale</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6" id="budget-chart-col">
                                <h6 class="text-center mb-3">Répartition des budgets par programme</h6>
                                <div class="chart-container">
                                    <canvas id="budgetChart"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6" id="execution-chart-col">
                                <h6 class="text-center mb-3">Taux d'exécution par programme</h6>
                                <div class="chart-container">
                                    <canvas id="executionChart"></canvas>
                                </div>
                            </div>
                            <div class="col-12 mt-4" id="synthesis-trimestriel-chart-col">
                                <h6 class="text-center mb-3">Suivi trimestriel des programmes (Synthèse)</h6>
                                <div class="chart-container">
                                    <canvas id="trimestrielChart"></canvas>
                                </div>
                            </div>
                            <!-- Dynamic containers for individual program quarterly charts -->
                            <div class="col-12 mt-4 row" id="individual-trimestriel-charts-container">
                                <!-- Individual charts will be appended here by JS -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <a href="/" class="return-home-button" title="Retour au site web">
        <i class='bx bx-home'></i>
    </a>

    <!-- Loading Spinner Overlay -->
    <div class="spinner-overlay" id="loadingSpinner">
        <div class="spinner-border text-primary spinner-border-lg" role="status">
            <span class="visually-hidden">Chargement...</span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Libraries for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <!-- Library for XLSX generation -->
    <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

    <script>
        // --- GLOBAL VARIABLES ---
        let budgetChartInstance = null;
        let executionChartInstance = null;
        let trimestrielChartInstance = null;
        let individualTrimestrielChartInstances = {}; // Store instances of individual program charts
        let allProgramsData = []; // This will now hold the programs already filtered by year and bailleur
        let currentFilteredPrograms = []; // New: To store the programs currently displayed in the table
        let authToken = null; // Variable to store the authentication token
        let hamburgerToggle, sidebarWrapper, sidebarOverlay, yearFilter, programSearch, loadingSpinner;
        let programSelectForSynthesis, toggleSynthesisChart; // New global variables for chart controls
        let etatFilter; // Global variable for the new status filter
        let individualChartsContainer; // Global reference for individual charts container
        let manuallySelectedSynthesisPrograms = []; // New: To store manually selected program IDs

        // --- CORRECTION: Define an explicit base URL for your Laravel application ---
        const LARAVEL_APP_URL = "{{ url('/') }}"; // Use Laravel's URL generator for robustness

        // --- LOADING SPINNER MANAGEMENT ---
        const showSpinner = () => {
            if (loadingSpinner) {
                loadingSpinner.classList.add('show');
            }
        };

        const hideSpinner = () => {
            if (loadingSpinner) {
                loadingSpinner.classList.remove('show');
            }
        };

        // --- SINGLE AUTHENTICATION ---
        const loginAndGetToken = async () => {
            if (authToken) {
                console.log("DEBUG: loginAndGetToken: Token déjà présent. Saut de la connexion.");
                return true;
            }
            try {
                // Use the explicit base URL for the login proxy
                const loginUrl = `${LARAVEL_APP_URL}/proxy/login`;
                console.log("DEBUG: loginAndGetToken: Tentative de connexion via le proxy à l'URL :", loginUrl);
                
                const loginResponse = await axios.post(loginUrl); 
                
                console.log("DEBUG: loginAndGetToken: Réponse brute de la connexion:", loginResponse.data); // Log the full response
                
                if (loginResponse.data && loginResponse.data.token) { 
                    authToken = loginResponse.data.token;
                    axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`; // Set for all subsequent requests
                    console.log("DEBUG: loginAndGetToken: Authentification réussie. Token stocké.");
                    return true;
                } else {
                    console.error("ERROR: loginAndGetToken: Le proxy n'a pas retourné un token valide. Réponse reçue:", loginResponse.data);
                    return false;
                }
            } catch (error) {
                console.error("ERROR: loginAndGetToken: Erreur critique pendant la connexion via proxy:", error.response ? error.response.data : error.message);
                return false;
            }
        };

        /**
         * Function that filters and restructures reform data by year and funder.
         * @param {Object} listAnneeJson - JSON containing the list of years. (Deprecated, kept for compatibility)
         * @param {Object} bailleursJson - JSON containing the list of funders.
         * @param {Object} bailleurDataJson - JSON containing program data for a funder.
         * @param {Object} reformesJson - JSON containing reform data.
         * @param {Number|string} annee_param - Year to filter by, or "all".
         * @param {Number} bailleur_id - ID of the funder to filter by.
         * @return {Object} - Restructured JSON with filtered program data.
         */
        function filtrerReformeParAnneeBailleur(listAnneeJson, bailleursJson, bailleurDataJson, reformesJson, annee_param, bailleur_id) {
            const resultat = {
                annee: annee_param,
                bailleur_id: bailleur_id,
                programmes: []
            };
            
            const bailleurExiste = bailleursJson.records.some(b => b.id == bailleur_id);
            if (!bailleurExiste || !bailleurDataJson.success || !bailleurDataJson.data || bailleurDataJson.data.bailleur.id != bailleur_id) {
                console.warn("WARN: filtrerReformeParAnneeBailleur: Bailleur introuvable ou données de bailleur invalides.");
                return resultat;
            }

            const relevantProgramIdsFromReformes = new Set();
            if (reformesJson.data && Array.isArray(reformesJson.data)) {
                reformesJson.data.forEach(reforme => {
                    const reformeStartYear = reforme.exercice_annee_debut;
                    const reformeEndYear = reforme.exercice_annee_fin;
                    
                    const matchesYearFilter = annee_param === "all" ||
                                            (reformeStartYear && reformeEndYear && !isNaN(reformeStartYear) && !isNaN(reformeEndYear) &&
                                            parseInt(annee_param, 10) >= reformeStartYear && parseInt(annee_param, 10) <= reformeEndYear);

                    if (matchesYearFilter) {
                        reforme.programmes?.forEach(reformeProgram => {
                            relevantProgramIdsFromReformes.add(reformeProgram.id);
                        });
                    }
                });
            } else {
                console.warn("WARN: filtrerReformeParAnneeBailleur: reformesJson.data est manquant ou n'est pas un tableau.");
            }

            if (bailleurDataJson.data.programmes && Array.isArray(bailleurDataJson.data.programmes)) {
                bailleurDataJson.data.programmes.forEach(programData => {
                    if (relevantProgramIdsFromReformes.has(programData.id)) {
                        const processedActions = programData.actions.map(action => ({
                            ...action,
                            activites: action.activites.map(activite => ({
                                ...activite,
                                suivi_execution: {
                                    global: {
                                        taux_execution_physique: parseFloat(activite.suivi_execution?.global?.taux_execution_physique || 0),
                                        taux_execution_financier: parseFloat(activite.suivi_execution?.global?.taux_execution_financier || 0)
                                    },
                                    par_trimestre: activite.suivi_execution?.par_trimestre?.map(trim => ({...trim, taux_execution_physique: parseFloat(trim.taux_execution_physique || 0), taux_execution_financier: parseFloat(trim.taux_execution_financier || 0)})) || []
                                },
                                financement: {
                                    budget_national: parseFloat(activite.financement?.budget_national || 0),
                                    ptf: parseFloat(activite.financement?.ptf || 0),
                                    autres: parseFloat(activite.financement?.autres || 0),
                                    total: parseFloat(activite.financement?.total || 0)
                                }
                            }))
                        }));
                        resultat.programmes.push({ ...programData, actions: processedActions });
                    }
                });
            } else {
                console.warn("WARN: filtrerReformeParAnneeBailleur: bailleurDataJson.data.programmes est manquant ou n'est pas un tableau.");
            }
            return resultat;
        }

        const calculateProgramProgression = (program) => {
            let progression = 0;
            let totalActivitiesWithProgress = 0;
            program.actions.forEach(action => {
                action.activites.forEach(activite => {
                    const tauxExecution = parseFloat(activite.suivi_execution?.global?.taux_execution_physique || 0);
                    if (!isNaN(tauxExecution)) {
                        progression += tauxExecution;
                        totalActivitiesWithProgress++;
                    }
                });
            });
            return totalActivitiesWithProgress > 0 ? Math.round(progression / totalActivitiesWithProgress) : 0;
        };

        const getProgramStatus = (progressionMoyenne) => {
            if (progressionMoyenne === 0) return { text: 'Non démarré', class: 'bg-secondary', value: 'not_started' };
            if (progressionMoyenne === 100) return { text: 'Terminé', class: 'bg-success', value: 'completed' };
            // Correction: "Démarré" et "En cours" sont maintenant un seul état "En cours"
            if (progressionMoyenne > 0 && progressionMoyenne < 100) return { text: 'En cours', class: 'bg-primary', value: 'in_progress' };
            return { text: 'Inconnu', class: 'bg-dark', value: 'unknown' }; // Fallback
        };

        // Function to apply all active filters and update the dashboard
        const applyFiltersAndSearch = () => {
            const searchTerm = programSearch.value.toLowerCase();
            let tempFilteredPrograms = allProgramsData.filter(program => {
                return !searchTerm || 
                       (program.intitule && program.intitule.toLowerCase().includes(searchTerm)) ||
                       (program.actions && program.actions.some(action => 
                           (action.libelle && action.libelle.toLowerCase().includes(searchTerm)) ||
                           (action.activites && action.activites.some(activite => 
                               (activite.libelle && activite.libelle.toLowerCase().includes(searchTerm))
                           ))
                       ));
            });

            const selectedStatuses = Array.from(etatFilter.selectedOptions).map(option => option.value);
            
            if (!selectedStatuses.includes('all')) {
                tempFilteredPrograms = tempFilteredPrograms.filter(program => {
                    const progressionMoyenne = calculateProgramProgression(program);
                    const programStatus = getProgramStatus(progressionMoyenne);
                    return selectedStatuses.includes(programStatus.value);
                });
            }
            
            // Store the currently filtered programs for download functions
            currentFilteredPrograms = tempFilteredPrograms; 

            updateDashboardCards(currentFilteredPrograms);
            updateProgrammesTables(currentFilteredPrograms);
            updateTreeView(currentFilteredPrograms);
            updateCharts(currentFilteredPrograms); // Pass the fully filtered programs
        };

        const updateDashboardCards = (programs) => {
            const totalProgrammes = programs.length;
            const totalActions = programs.reduce((acc, prog) => acc + (prog.actions?.length || 0), 0);
            const totalActivites = programs.reduce((acc, prog) => acc + (prog.actions?.reduce((sum, action) => sum + (action.activites?.length || 0), 0) || 0), 0);

            const cards = document.querySelectorAll('#dashboard-cards .card');
            if (cards[0]) cards[0].querySelector('h2').textContent = totalProgrammes;
            if (cards[1]) cards[1].querySelector('h2').textContent = totalActions;
            if (cards[2]) cards[2].querySelector('h2').textContent = totalActivites;
        };

        const updateProgrammesTables = (programmesToDisplay) => {
            const tableBody = document.querySelector('#programs-table-body');
            tableBody.innerHTML = '';

            // Remove existing modals before creating new ones to prevent duplicates
            document.querySelectorAll('.actions-modal, .activite-modal').forEach(modal => modal.remove());

            if (programmesToDisplay.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Aucun programme à afficher pour les filtres sélectionnés.</td></tr>';
                return;
            }

            programmesToDisplay.forEach((programme) => {
                const progressionMoyenne = calculateProgramProgression(programme);
                const programStatus = getProgramStatus(progressionMoyenne);
                const etatBadge = `<span class="badge ${programStatus.class}">${programStatus.text}</span>`;
                const actionsModalId = `actionsModal${programme.id}`; 

                const modalHtml = `
                    <div class="modal actions-modal fade" id="${actionsModalId}" tabindex="-1" aria-labelledby="actionsModalLabel${programme.id}" aria-hidden="true">
                        <div class="modal-dialog modal-lg"><div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="actionsModalLabel${programme.id}">Actions: ${programme.intitule}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                            </div>
                            <div class="modal-body"><div class="table-responsive"><table class="table table-striped"><thead><tr><th>Action</th><th>Objectif</th><th>Progression</th><th>État</th></tr></thead><tbody>
                                ${programme.actions?.map(action => {
                                    const actionProgression = Math.round((action.activites?.reduce((acc, act) => acc + parseFloat(act.suivi_execution?.global?.taux_execution_physique || 0), 0) || 0) / (action.activites?.length || 1));
                                    const actionStatus = getProgramStatus(actionProgression);
                                    return `<tr><td>${action.libelle}</td><td>${action.objectif || 'N/D'}</td><td><div class="progress"><div class="progress-bar" style="width:${actionProgression}%">${actionProgression}%</div></div></td><td><span class="badge ${actionStatus.class}">${actionStatus.text}</span></td></tr>`;
                                }).join('') || '<tr><td colspan="4">Aucune action.</td></tr>'}
                            </tbody></table></div></div>
                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button></div>
                        </div></div>
                    </div>`;
                document.body.insertAdjacentHTML('beforeend', modalHtml);

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${programme.intitule}</td>
                    <td><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#${actionsModalId}"><i class="bx bx-list-ul"></i> <span translate="no">Consulter</span></button></td>
                    <td><div class="progress"><div class="progress-bar" style="width: ${progressionMoyenne}%">${progressionMoyenne}%</div></div></td>
                    <td>${etatBadge}</td>`;
                tableBody.appendChild(row);
            });
        };

        const updateTreeView = (programmes) => {
            const programmeTreeDiv = document.getElementById('programme-tree');
            programmeTreeDiv.innerHTML = '';

            if (programmes.length === 0) {
                programmeTreeDiv.innerHTML = '<p class="text-center text-muted mt-3">Aucun programme à afficher.</p>';
                return;
            }

            programmes.forEach((programme) => {
                const programmeId = `programme-${programme.id}`;
                let programmeHtml = `<div class="programme-item">
                    <div class="d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#${programmeId}" role="button" aria-expanded="false" aria-controls="${programmeId}">
                        <span><i class="bi bi-folder-fill me-2"></i><strong>${programme.intitule}</strong></span>
                        <i class="bi bi-chevron-down toggle-icon"></i>
                    </div><div class="collapse" id="${programmeId}"><div class="ms-4">`;

                programme.actions?.forEach((action) => {
                    const actionId = `action-${programme.id}-${action.id}`;
                    programmeHtml += `<div class="action-item">
                        <div class="d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#${actionId}" role="button" aria-expanded="false" aria-controls="${actionId}">
                            <span><i class="bi bi-card-checklist me-2"></i> ${action.libelle}</span>
                            <i class="bi bi-chevron-down toggle-icon"></i>
                        </div><div class="collapse" id="${actionId}"><div class="ms-4">`;
                    
                    action.activites?.forEach((activite) => {
                        const activiteId = `activite-modal-${programme.id}-${action.id}-${activite.id}`;
                        programmeHtml += `<div class="activite-item"><a href="#" class="btn-link" data-bs-toggle="modal" data-bs-target="#${activiteId}"><i class="bi bi-check-circle-fill me-2"></i> ${activite.libelle}</a></div>`;
                        const formatCurrency = (value) => value ? parseFloat(value).toLocaleString('fr-FR', { style: 'currency', currency: 'XOF', minimumFractionDigits: 2, maximumFractionDigits: 2 }) : 'N/A';
                        
                        // Group trimestres by their main quarter (e.g., T1, T2)
                        const groupedTrimestres = {};
                        activite.suivi_execution?.par_trimestre?.forEach(t => {
                            const mainQuarterKey = t.trimestre.substring(0, 2); // Extracts "T1", "T2", etc.
                            const subQuarterLabel = t.trimestre.substring(2);    // Extracts "Q1", "T1" (if user data is "T2T1") etc.
                            if (!groupedTrimestres[mainQuarterKey]) {
                                groupedTrimestres[mainQuarterKey] = [];
                            }
                            groupedTrimestres[mainQuarterKey].push({
                                originalTrimestre: t.trimestre,
                                subQuarter: subQuarterLabel,
                                taux_execution_physique: t.taux_execution_physique,
                                taux_execution_financier: t.taux_execution_financier
                            });
                        });

                        // Generate table rows for grouped trimestres
                        const trimestresTableRows = Object.keys(groupedTrimestres).sort().map(mainQuarter => {
                            const subQuarters = groupedTrimestres[mainQuarter];
                            let rowsHtml = '';
                            subQuarters.forEach((t, index) => {
                                rowsHtml += `
                                    <tr>
                                        ${index === 0 ? `<td rowspan="${subQuarters.length}" class="fw-bold align-middle bg-light text-center">${mainQuarter}</td>` : ''}
                                        <td>${t.subQuarter}</td>
                                        <td>${parseFloat(t.taux_execution_physique || 0).toFixed(2)}</td>
                                        <td>${parseFloat(t.taux_execution_financier || 0).toFixed(2)}</td>
                                    </tr>
                                `;
                            });
                            return rowsHtml;
                        }).join('') || '<tr><td colspan="4" class="text-center">Aucun suivi trimestriel.</td></tr>'; // 4 columns now

                        // Start of the modal HTML for activity details
                        const activiteModalHtml = `<div class="modal activite-modal fade" id="${activiteId}" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
                            <div class="modal-header bg-primary text-white"><h5 class="modal-title">Détails: ${activite.libelle}</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body">
                                <p><strong>Objectif:</strong> ${activite.objectif || 'N/D'}</p>
                                <p><strong>Budget Total:</strong> ${formatCurrency(activite.financement?.total)}</p>
                                <p><strong>Taux d'exécution:</strong> ${parseFloat(activite.suivi_execution?.global?.taux_execution_physique || 0).toFixed(2)}%</p>
                                <h6>Suivi trimestriel:</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>Trimestre Principal</th>
                                                <th>Sous-Trimestre</th>
                                                <th>Physique (%)</th>
                                                <th>Financier (%)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${trimestresTableRows}
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex flex-wrap gap-2 mt-4">
                                    <button class="btn btn-sm btn-info text-white" onclick="exportActivityDetails(event, '${activiteId}', 'word')"><i class='bx bx-file-blank'></i> Word</button>
                                    <button class="btn btn-sm btn-danger" onclick="exportActivityDetails(event, '${activiteId}', 'pdf')"><i class='bx bxs-file-pdf'></i> PDF</button>
                                    <button class="btn btn-sm btn-success text-white" onclick="exportActivityDetails(event, '${activiteId}', 'excel')"><i class='bx bxs-file-excel'></i> Excel</button>
                                    <button class="btn btn-sm btn-secondary" onclick="exportActivityDetails(event, '${activiteId}', 'txt')"><i class='bx bx-file'></i> TXT</button>
                                </div>
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button></div>
                        </div></div></div>`;
                        document.body.insertAdjacentHTML('beforeend', activiteModalHtml);
                    });
                    programmeHtml += `</div></div></div>`;
                });
                programmeHtml += `</div></div></div>`;
                programmeTreeDiv.insertAdjacentHTML('beforeend', programmeHtml);
            });
            
            // Re-attach custom toggle icon listeners
            programmeTreeDiv.querySelectorAll('.programme-item > .d-flex, .action-item > .d-flex').forEach(trigger => {
                trigger.addEventListener('click', function() {
                    const toggleIcon = this.querySelector('.toggle-icon');
                    // Check if the collapse target is actually shown or hidden by Bootstrap
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        // Use Bootstrap's Collapse API to check the state
                        const bsCollapse = bootstrap.Collapse.getInstance(targetElement); // Get existing instance
                        if (bsCollapse) {
                            // If an instance exists, toggle the icon based on its state
                            if (targetElement.classList.contains('show')) {
                                toggleIcon.classList.remove('bi-chevron-up');
                                toggleIcon.classList.add('bi-chevron-down');
                            } else {
                                toggleIcon.classList.remove('bi-chevron-down');
                                toggleIcon.classList.add('bi-chevron-up');
                            }
                        } else {
                            // Fallback if no instance (shouldn't happen with proper initialization)
                            toggleIcon.classList.toggle('bi-chevron-up');
                        }
                    } else {
                        // Fallback if target element not found
                        toggleIcon.classList.toggle('bi-chevron-up');
                    }
                });
            });

            // Explicitly initialize Bootstrap Collapse components after they are added
            // This is often needed for dynamically added content, despite MutationObserver in BS5
            programmeTreeDiv.querySelectorAll('.collapse').forEach(collapseEl => {
                new bootstrap.Collapse(collapseEl, {
                    toggle: false // Don't toggle immediately on init
                });
            });
        };

        const getTrimestrielDataForPrograms = (programsToAggregate) => {
            const aggregatedPhysical = { 'T1': [], 'T2': [], 'T3': [], 'T4': [] };
            const aggregatedFinancial = { 'T1': [], 'T2': [], 'T3': [], 'T4': [] };

            programsToAggregate.forEach(program => {
                program.actions?.forEach(action => {
                    action.activites?.forEach(activite => {
                        activite.suivi_execution?.par_trimestre?.forEach(q => {
                            const quarterKey = q.trimestre.substring(0, 2);
                            if (aggregatedPhysical[quarterKey]) aggregatedPhysical[quarterKey].push(parseFloat(q.taux_execution_physique || 0));
                            if (aggregatedFinancial[quarterKey]) aggregatedFinancial[quarterKey].push(parseFloat(q.taux_execution_financier || 0));
                        });
                    });
                });
            });

            const trimestrielLabels = ['T1', 'T2', 'T3', 'T4'];
            const avgPhysical = trimestrielLabels.map(label => {
                const values = aggregatedPhysical[label];
                return values.length > 0 ? Math.round(values.reduce((s, v) => s + v, 0) / values.length) : 0;
            });
            const avgFinancial = trimestrielLabels.map(label => {
                const values = aggregatedFinancial[label];
                return values.length > 0 ? Math.round(values.reduce((s, v) => s + v, 0) / values.length) : 0;
            });
            return { trimestrielLabels, avgPhysical, avgFinancial };
        };

        const updateCharts = (programs) => {
            // Destroy existing chart instances
            if (budgetChartInstance) budgetChartInstance.destroy();
            if (executionChartInstance) executionChartInstance.destroy();
            if (trimestrielChartInstance) trimestrielChartInstance.destroy();
            Object.values(individualTrimestrielChartInstances).forEach(chart => chart.destroy());
            individualTrimestrielChartInstances = {};
            individualChartsContainer.innerHTML = ''; // Clear individual charts

            const budgetCtx = document.getElementById('budgetChart')?.getContext('2d');
            const executionCtx = document.getElementById('executionChart')?.getContext('2d');
            const trimestrielCtx = document.getElementById('trimestrielChart')?.getContext('2d');
            const synthesisChartCol = document.getElementById('synthesis-trimestriel-chart-col');

            const selectedProgramIdsForSynthesis = Array.from(programSelectForSynthesis.selectedOptions).map(option => parseInt(option.value));

            // Determine which programs should be displayed in ALL charts (Budget, Execution, Synthesis Quarterly)
            let programsToUseForCharts = [];
            if (toggleSynthesisChart.checked) {
                // If global synthesis is checked, use all currently filtered programs
                programsToUseForCharts = programs;
                synthesisChartCol.style.display = 'block'; // Ensure synthesis chart is visible
                individualChartsContainer.innerHTML = ''; // Ensure individual charts are cleared/hidden
            } else {
                // If global synthesis is unchecked, use only the manually selected programs
                programsToUseForCharts = programs.filter(p => selectedProgramIdsForSynthesis.includes(p.id));
                // Only show synthesis chart if there are selected programs, otherwise hide it.
                synthesisChartCol.style.display = programsToUseForCharts.length > 0 ? 'block' : 'none';
            }

            // Prepare data using programsToUseForCharts for all charts
            const programNames = programsToUseForCharts.map(p => p.intitule);
            const budgetData = programsToUseForCharts.map(p => p.actions?.reduce((acc, action) => acc + (action.activites?.reduce((s, act) => s + parseFloat(act.financement?.total || 0), 0) || 0), 0) || 0);
            const executionPhysicalData = programsToUseForCharts.map(p => calculateProgramProgression(p));

            if (budgetCtx && programsToUseForCharts.length > 0) {
                budgetChartInstance = new Chart(budgetCtx, {
                    type: 'pie',
                    data: {
                        labels: programNames,
                        datasets: [{
                            data: budgetData,
                            backgroundColor: ['#007bff', '#28a745', '#17a2b8', '#ffc107', '#fd7e14', '#6c757d', '#dc3545']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right'
                            },
                            tooltip: { // Enable tooltips for pie chart
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed !== null) {
                                            label += new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(context.parsed);
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            } else if (budgetCtx) {
                budgetChartInstance = null; // Mark as null
                budgetCtx.clearRect(0, 0, budgetCtx.canvas.width, budgetCtx.canvas.height); // Clear canvas
            }

            if (executionCtx && programsToUseForCharts.length > 0) {
                executionChartInstance = new Chart(executionCtx, {
                    type: 'bar',
                    data: {
                        labels: programNames,
                        datasets: [{
                            label: 'Taux exécution physique',
                            data: executionPhysicalData,
                            backgroundColor: 'rgba(40, 167, 69, 0.7)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100
                            }
                        },
                        plugins: {
                            tooltip: { // Enable tooltips for bar chart
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y + '%';
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            } else if (executionCtx) {
                executionChartInstance = null; // Mark as null
                executionCtx.clearRect(0, 0, executionCtx.canvas.width, executionCtx.canvas.height); // Clear canvas
            }

            // Trimestriel chart (Synthesis)
            if (trimestrielCtx && programsToUseForCharts.length > 0) {
                const { trimestrielLabels, avgPhysical, avgFinancial } = getTrimestrielDataForPrograms(programsToUseForCharts);

                trimestrielChartInstance = new Chart(trimestrielCtx, {
                    type: 'line',
                    data: {
                        labels: trimestrielLabels,
                        datasets: [
                            { label: 'Taux d\'exécution physique (Moyenne)', data: avgPhysical, borderColor: '#007bff', fill: false },
                            { label: 'Taux d\'exécution financier (Moyenne)', data: avgFinancial, borderColor: '#28a745', fill: false }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true, max: 100 } },
                        plugins: {
                            tooltip: { // Enable tooltips for line chart
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y + '%';
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            } else if (trimestrielCtx) {
                trimestrielChartInstance = null; // Mark as null
                trimestrielCtx.clearRect(0, 0, trimestrielCtx.canvas.width, trimestrielCtx.canvas.height); // Clear canvas
            }

            // Generate individual charts if synthesis toggle is OFF and specific programs are selected
            if (!toggleSynthesisChart.checked && programsToUseForCharts.length > 0) {
                programsToUseForCharts.forEach(program => {
                    const programQuarterlyData = getTrimestrielDataForPrograms([program]); // Get data for single program
                    const chartId = `trimestrielChart-${program.id}`;
                    const chartContainerHtml = `
                        <div class="col-md-6 mt-4">
                            <h6 class="text-center mb-3">Suivi trimestriel: ${program.intitule}</h6>
                            <div class="chart-container">
                                <canvas id="${chartId}"></canvas>
                            </div>
                        </div>`;
                    individualChartsContainer.insertAdjacentHTML('beforeend', chartContainerHtml);

                    const individualCtx = document.getElementById(chartId)?.getContext('2d');
                    if (individualCtx) {
                        individualTrimestrielChartInstances[program.id] = new Chart(individualCtx, {
                            type: 'line',
                            data: {
                                labels: programQuarterlyData.trimestrielLabels,
                                datasets: [
                                    { label: 'Taux d\'exécution physique', data: programQuarterlyData.avgPhysical, borderColor: '#17a2b8', fill: false },
                                    { label: 'Taux d\'exécution financier', data: programQuarterlyData.avgFinancial, borderColor: '#ffc107', fill: false }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: { y: { beginAtZero: true, max: 100 } },
                                plugins: {
                                    tooltip: { // Enable tooltips for individual line chart
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.dataset.label || '';
                                                if (label) {
                                                    label += ': ';
                                                }
                                                if (context.parsed.y !== null) {
                                                    label += context.parsed.y + '%';
                                                }
                                                return label;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                });
            }
        };

        const populateProgramSynthesisSelect = (programs) => {
            programSelectForSynthesis.innerHTML = ''; // Clear existing options
            if (programs.length === 0) {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Aucun programme disponible';
                option.disabled = true;
                programSelectForSynthesis.appendChild(option);
                return;
            }

            programs.forEach(program => {
                const option = document.createElement('option');
                option.value = program.id;
                option.textContent = program.intitule;
                // We do NOT set option.selected here. Selection logic is handled by updateSynthesisSelectionState.
                programSelectForSynthesis.appendChild(option);
            });
        };

        // Manages the disabled state and selection of the synthesis programs dropdown
        const updateSynthesisSelectionState = () => {
            programSelectForSynthesis.disabled = toggleSynthesisChart.checked; // Disable if global synthesis is checked

            if (toggleSynthesisChart.checked) { // If "Afficher la synthèse globale" is checked
                // Select all options programmatically
                Array.from(programSelectForSynthesis.options).forEach(option => {
                    option.selected = true;
                });
                manuallySelectedSynthesisPrograms = []; // Clear manual selections as global is active
            } else { // If "Afficher la synthèse globale" is NOT checked (manual selection mode)
                // Deselect all initially to accurately apply stored manual selections
                Array.from(programSelectForSynthesis.options).forEach(option => {
                    option.selected = false;
                });
                // Restore previous manual selections
                Array.from(programSelectForSynthesis.options).forEach(option => {
                    if (manuallySelectedSynthesisPrograms.includes(parseInt(option.value))) {
                        option.selected = true;
                    }
                });
            }
        };

        const loadAnnneeData = async () => {
            const select = document.getElementById('yearFilter'); 
            if (!select) return;

            if (!authToken) {
                console.error('ERROR: loadAnnneeData: Échec, token d\'authentification manquant. Impossible de charger les années.');
                select.innerHTML = '<option value="">Auth a échoué</option>';
                select.disabled = true;
                return;
            }

            try {
                // Use the explicit base URL for the proxy
                const yearListUrl = `${LARAVEL_APP_URL}/proxy/annees`;
                console.log("DEBUG: loadAnnneeData: Tentative de récupération des années depuis :", yearListUrl);

                const response = await axios.get(yearListUrl);
                
                console.log("DEBUG: loadAnnneeData: Réponse brute de l'API pour /proxy/annees :", response.data); // Log the full response

                if (response.data && Array.isArray(response.data.records) && response.data.records.length > 0) {
                    const yearsArray = response.data.records;
                    select.innerHTML = `<option value="all">-- Toutes les années --</option>`;
                    
                    yearsArray.sort((a, b) => parseInt(b.annee) - parseInt(a.annee));
                    
                    yearsArray.forEach(annee => {
                        if (annee.annee) { // Ensure 'annee' property exists
                            const option = document.createElement('option');
                            option.value = annee.annee.toString();
                            option.textContent = annee.annee.toString(); 
                            select.appendChild(option);
                        }
                    });

                    select.value = new Date().getFullYear().toString(); // Select current year if available
                    if (!select.value) select.value = "all"; // Fallback to all

                    select.disabled = false;
                    console.log('DEBUG: loadAnnneeData: Années chargées avec succès.');
                } else {
                    console.warn('WARN: loadAnnneeData: Aucune année valide trouvée ou le format est inattendu dans la réponse de l\'API.', response.data);
                    select.innerHTML = '<option value="">Pas de données</option>';
                    select.disabled = true;
                }
            } catch (error) {
                console.error('ERROR: loadAnnneeData: Erreur lors du chargement des années :', error.response ? error.response.data : error.message);
                select.innerHTML = `<option value="">Erreur API</option>`;
                select.disabled = true;
            }
        };

        const fetchData = async () => {
            showSpinner();
            try {
                if (!authToken) {
                    document.querySelector('#programs-table-body').innerHTML = '<tr><td colspan="4" class="text-center text-danger">Authentification échouée. Impossible de charger les données.</td></tr>';
                    hideSpinner();
                    return;
                }
                
                const selectedAnnee = yearFilter.value;
                const bailleurId = 1; // Assuming bailleur ID is fixed for this dashboard

                let reformesStatsUrl = `${LARAVEL_APP_URL}/proxy/reformes-stats`;
                if (selectedAnnee !== "all") {
                    reformesStatsUrl += `?annee_id=${selectedAnnee}`;
                }

                console.log("DEBUG: fetchData: Récupération des données pour l'année:", selectedAnnee);
                console.log("DEBUG: fetchData: Appels API: bailleurs, bailleur/data, reformes-stats");

                const [bailleursResponse, bailleurDataResponse, reformesResponse] = await Promise.all([
                    axios.get(`${LARAVEL_APP_URL}/proxy/bailleurs`),
                    axios.get(`${LARAVEL_APP_URL}/proxy/bailleur/data`),
                    axios.get(reformesStatsUrl)
                ]);

                console.log("DEBUG: fetchData: Réponses brutes des API:");
                console.log("DEBUG:   bailleursResponse:", bailleursResponse.data);
                console.log("DEBUG:   bailleurDataResponse:", bailleurDataResponse.data);
                console.log("DEBUG:   reformesResponse:", reformesResponse.data);

                // Assuming listAnnee is not needed for filtering as we have the year from the dropdown
                const filteredResult = filtrerReformeParAnneeBailleur(
                    {records: []}, // Placeholder, not used in the logic
                    bailleursResponse.data,
                    bailleurDataResponse.data,
                    reformesResponse.data,
                    selectedAnnee,
                    bailleurId
                );
                
                allProgramsData = filteredResult.programmes;
                console.log("DEBUG: fetchData: Programmes filtrés (allProgramsData):", allProgramsData);

                // 1. Populate the options in the select element
                populateProgramSynthesisSelect(allProgramsData); 
                // 2. Set the disabled state and initial selection based on the toggle
                updateSynthesisSelectionState(); 
                // 3. Apply all filters and render charts/tables
                applyFiltersAndSearch();

            } catch (error) {
                console.error('ERROR: fetchData: Erreur lors du chargement des données:', error);
                const errorMessage = `Erreur API: ${error.message}. Vérifiez la console pour plus de détails.`;
                document.querySelector('#programs-table-body').innerHTML = `<tr><td colspan="4" class="text-center text-danger">${errorMessage}</td></tr>`;
            } finally {
                hideSpinner();
            }
        };

        const initializeDashboard = async () => {
            showSpinner();
            try {
                const isAuthenticated = await loginAndGetToken();
                if (isAuthenticated) {
                    yearFilter.disabled = false;
                    programSearch.disabled = false;
                    // programSelectForSynthesis.disabled will be set by updateSynthesisSelectionState
                    
                    await loadAnnneeData();
                    await fetchData();
                } else {
                    const errorMsg = '<div class="alert alert-danger mt-5"><strong>Authentification échouée.</strong> Impossible de charger le tableau de bord. Veuillez vérifier vos identifiants ou le proxy.</div>';
                    document.querySelector('.main-content .container-fluid').innerHTML = errorMsg;
                }
            } finally {
                hideSpinner();
            }
        };

        // --- DOWNLOAD FUNCTIONS (FOR ACTIVITY MODAL) ---

        const getActiviteDetailsForExport = (activity) => {
            // Updated formatCurrency to ensure two decimal places
            const formatCurrency = (value) => value ? parseFloat(value).toLocaleString('fr-FR', { style: 'currency', currency: 'XOF', minimumFractionDigits: 2, maximumFractionDigits: 2 }) : 'N/A';

            // Define groupedTrimestres here so it's available for HTML and Excel export
            const groupedTrimestres = {};
            activity.suivi_execution?.par_trimestre?.forEach(t => {
                const mainQuarterKey = t.trimestre.substring(0, 2);
                const subQuarterLabel = t.trimestre.substring(2);
                if (!groupedTrimestres[mainQuarterKey]) {
                    groupedTrimestres[mainQuarterKey] = [];
                }
                groupedTrimestres[mainQuarterKey].push({
                    originalTrimestre: t.trimestre,
                    subQuarter: subQuarterLabel,
                    taux_execution_physique: t.taux_execution_physique,
                    taux_execution_financier: t.taux_execution_financier
                });
            });


            let txtContent = `Détails de l'Activité: ${activity.libelle}\n`;
            txtContent += `Objectif: ${activity.objectif || 'N/D'}\n`;
            txtContent += `Budget Total: ${formatCurrency(activity.financement?.total)}\n`;
            txtContent += `Taux d'exécution: ${parseFloat(activity.suivi_execution?.global?.taux_execution_physique || 0).toFixed(2)}%\n`;
            txtContent += `\nSuivi trimestriel:\n`;
            // For TXT, we still print line by line, but with the correct formatting
            Object.keys(groupedTrimestres).sort().forEach(mainQuarter => {
                const subQuarters = groupedTrimestres[mainQuarter];
                subQuarters.forEach(t => {
                    txtContent += `${mainQuarter}${t.subQuarter}: Physique ${parseFloat(t.taux_execution_physique || 0).toFixed(2)}%, Financier ${parseFloat(t.taux_execution_financier || 0).toFixed(2)}%\n`;
                });
            });

            let htmlContent = `
                <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
                    <head><meta charset='utf-8'></head>
                    <body>
                        <h1 style="font-family: sans-serif;">Détails de l'Activité: ${activity.libelle}</h1>
                        <p style="font-family: sans-serif;"><strong>Objectif:</strong> ${activity.objectif || 'N/D'}</p>
                        <p style="font-family: sans-serif;"><strong>Budget Total:</strong> ${formatCurrency(activity.financement?.total)}</p>
                        <p style="font-family: sans-serif;"><strong>Taux d'exécution:</strong> ${parseFloat(activity.suivi_execution?.global?.taux_execution_physique || 0).toFixed(2)}%</p>
                        <h2 style="font-family: sans-serif;">Suivi trimestriel:</h2>
                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-family: sans-serif;">
                            <thead>
                                <tr>
                                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2;">Trimestre Principal</th>
                                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2;">Sous-Trimestre</th>
                                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2;">Physique (%)</th>
                                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2;">Financier (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Grouped trimestres for Word export -->
                                ${Object.keys(groupedTrimestres).sort().map(mainQuarter => {
                                    const subQuarters = groupedTrimestres[mainQuarter];
                                    let rowsHtml = '';
                                    subQuarters.forEach((t, index) => {
                                        rowsHtml += `
                                            <tr>
                                                ${index === 0 ? `<td rowspan="${subQuarters.length}" style="border: 1px solid #ddd; padding: 8px; text-align: center; font-weight: bold; background-color: #e9ecef;">${mainQuarter}</td>` : ''}
                                                <td style="border: 1px solid #ddd; padding: 8px;">${t.subQuarter}</td>
                                                <td style="border: 1px solid #ddd; padding: 8px;">${parseFloat(t.taux_execution_physique || 0).toFixed(2)}</td>
                                                <td style="border: 1px solid #ddd; padding: 8px;">${parseFloat(t.taux_execution_financier || 0).toFixed(2)}</td>
                                            </tr>
                                        `;
                                    });
                                    return rowsHtml;
                                }).join('') || '<tr><td colspan="4">Aucun suivi trimestriel.</td></tr>'}
                            </tbody>
                        </table>
                    </body>
                </html>`;

            const excelData = [
                ["Détails de l'Activité", activity.libelle],
                ["Objectif", activity.objectif || 'N/D'],
                // Ensure financial values are formatted as numbers for Excel, not currency strings
                ["Budget Total", parseFloat(activity.financement?.total || 0).toFixed(2)],
                ["Taux d'exécution", parseFloat(activity.suivi_execution?.global?.taux_execution_physique || 0).toFixed(2)],
                [], // Empty row for spacing
                ["Suivi trimestriel"],
                ["Trimestre Principal", "Sous-Trimestre", "Physique (%)", "Financier (%)"]
            ];
            // Re-group for Excel export similar to HTML, using the already computed groupedTrimestres
            Object.keys(groupedTrimestres).sort().forEach(mainQuarter => {
                const subQuarters = groupedTrimestres[mainQuarter];
                subQuarters.forEach((t, index) => {
                    excelData.push([
                        index === 0 ? mainQuarter : '',
                        t.subQuarter,
                        parseFloat(t.taux_execution_physique || 0).toFixed(2),
                        parseFloat(t.taux_execution_financier || 0).toFixed(2)
                    ]);
                });
            });

            return { txt: txtContent, html: htmlContent, excel: excelData };
        };

        const downloadFile = (data, filename, type) => {
            const blob = new Blob([data], { type: type });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        };

        const exportActivityDetails = async (event, activiteModalId, format) => {
            event.stopPropagation(); // Prevent modal from closing if button is within dismissal area

            const modalElement = document.getElementById(activiteModalId);
            if (!modalElement) {
                console.error("Modal element not found:", activiteModalId);
                return;
            }

            // Extract IDs from activiteModalId (e.g., "activite-modal-123-456-789")
            const parts = activiteModalId.split('-');
            if (parts.length !== 5 || parts[0] !== 'activite' || parts[1] !== 'modal') {
                console.error("Invalid activiteModalId format:", activiteModalId);
                return;
            }
            const programId = parseInt(parts[2], 10);
            const actionId = parseInt(parts[3], 10);
            const activiteDataId = parseInt(parts[4], 10);

            let foundActivity = null;

            // Use the extracted IDs to find the activity in allProgramsData
            for (const program of allProgramsData) {
                if (program.id === programId) {
                    for (const action of program.actions || []) {
                        if (action.id === actionId) {
                            foundActivity = (action.activites || []).find(act => act.id === activiteDataId);
                            if (foundActivity) break; // Found the activity
                        }
                    }
                }
                if (foundActivity) break; // Found the activity and its parent action/program
            }

            if (!foundActivity) {
                console.error("Activity data not found for parsed IDs: Program ID", programId, "Action ID", actionId, "Activity ID", activiteDataId, "in allProgramsData.");
                return;
            }

            const { txt, html, excel } = getActiviteDetailsForExport(foundActivity);
            const filename = `activite-${foundActivity.id}-${foundActivity.libelle.replace(/[^a-zA-Z0-9]/g, '_')}`;

            switch (format) {
                case 'txt':
                    downloadFile(txt, `${filename}.txt`, 'text/plain;charset=utf-8');
                    break;
                case 'word':
                    downloadFile(html, `${filename}.doc`, 'application/msword');
                    break;
                case 'pdf':
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF('p', 'pt', 'a4');
                    const contentToPrint = modalElement.querySelector('.modal-body');

                    if (contentToPrint) {
                        // Create a temporary cloning container off-screen
                        const tempDiv = document.createElement('div');
                        tempDiv.style.position = 'absolute';
                        tempDiv.style.left = '-9999px'; // Move off-screen
                        tempDiv.style.top = '-9999px';
                        tempDiv.style.width = contentToPrint.offsetWidth + 'px'; // Maintain original width
                        // Ensure content is visible within tempDiv for html2canvas
                        tempDiv.style.height = 'auto';
                        tempDiv.style.overflow = 'visible';
                        document.body.appendChild(tempDiv);

                        // Clone the content and append to the temporary div
                        const clonedContent = contentToPrint.cloneNode(true);
                        // Temporarily hide download buttons in the cloned content for cleaner PDF
                        clonedContent.querySelectorAll('.d-flex.flex-wrap.gap-2.mt-4').forEach(btn => {
                            btn.style.visibility = 'hidden';
                            btn.style.height = '0';
                            btn.style.overflow = 'hidden';
                        });
                        tempDiv.appendChild(clonedContent);

                        try {
                            const canvas = await html2canvas(clonedContent, {
                                scale: 2,
                                logging: true,
                                useCORS: true
                            });

                            const imgData = canvas.toDataURL('image/png');
                            const imgWidth = 595.28;
                            const pageHeight = 841.89;
                            const imgHeight = canvas.height * imgWidth / canvas.width;
                            let heightLeft = imgHeight;
                            let position = 0;

                            doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                            heightLeft -= pageHeight;

                            while (heightLeft >= 0) {
                                position = heightLeft - imgHeight;
                                doc.addPage();
                                doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                                heightLeft -= pageHeight;
                            }
                            doc.save(`${filename}.pdf`);
                        } catch (error) {
                            console.error("Error generating PDF:", error);
                        } finally {
                            // Clean up the temporary div
                            document.body.removeChild(tempDiv);
                        }
                    } else {
                        console.error("Modal body content not found for PDF export.");
                    }
                    break;
                case 'excel':
                    const ws = XLSX.utils.aoa_to_sheet(excel);
                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, "Détails Activité");
                    XLSX.writeFile(wb, `${filename}.xlsx`);
                    break;
                default:
                    console.warn("Format de téléchargement non supporté :", format);
            }
        };


        document.addEventListener('DOMContentLoaded', async () => {
            hamburgerToggle = document.getElementById('hamburgerToggle'); 
            sidebarWrapper = document.getElementById('sidebar-wrapper');
            sidebarOverlay = document.getElementById('sidebarOverlay'); 
            yearFilter = document.getElementById('yearFilter');
            programSearch = document.getElementById('programSearch');
            loadingSpinner = document.getElementById('loadingSpinner');
            programSelectForSynthesis = document.getElementById('programSelectForSynthesis');
            toggleSynthesisChart = document.getElementById('toggleSynthesisChart');
            etatFilter = document.getElementById('etatFilter');
            individualChartsContainer = document.getElementById('individual-trimestriel-charts-container'); // Initialize here

            const toggleSidebar = () => {
                const isActive = hamburgerToggle.classList.toggle('is-active');
                sidebarWrapper.classList.toggle('active', isActive);
                if (window.innerWidth < 992) {
                    sidebarOverlay.classList.toggle('active', isActive);
                    document.body.classList.toggle('sidebar-open-mobile', isActive);
                }
            };

            hamburgerToggle.addEventListener('click', toggleSidebar);
            sidebarOverlay.addEventListener('click', toggleSidebar);
            
            sidebarWrapper.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', (event) => {
                    const targetId = link.getAttribute('href');
                    if (targetId && targetId.startsWith('#')) {
                        const targetElement = document.querySelector(targetId);
                        if (targetElement) {
                            event.preventDefault();
                            targetElement.scrollIntoView({ behavior: 'smooth' });
                            if (sidebarWrapper.classList.contains('active') && window.innerWidth < 992) {
                                toggleSidebar(); 
                            }
                        }
                    }
                });
            });

            yearFilter.addEventListener('change', fetchData); 
            programSearch.addEventListener('input', applyFiltersAndSearch);
            etatFilter.addEventListener('change', applyFiltersAndSearch);

            // MODIFIED Event Listeners for Synthesis controls
            toggleSynthesisChart.addEventListener('change', () => {
                updateSynthesisSelectionState(); // Update the select box state (disabled/enabled, selection)
                applyFiltersAndSearch(); // Re-render charts based on the new new selection mode
            });
            
            programSelectForSynthesis.addEventListener('change', () => {
                // Only update manual selections if global synthesis is OFF
                if (!toggleSynthesisChart.checked) { 
                    manuallySelectedSynthesisPrograms = Array.from(programSelectForSynthesis.selectedOptions).map(option => parseInt(option.value));
                }
                applyFiltersAndSearch(); // Re-render charts based on manual selection
            });

            // Removed global download buttons as they are now per-activity
            // document.getElementById('downloadWord').addEventListener('click', exportToWord);
            // document.getElementById('downloadPdf').addEventListener('click', exportToPdf);
            // document.getElementById('downloadTxt').addEventListener('click', exportToTxt);


            await initializeDashboard();
        });
    </script>
</body>
</html>
