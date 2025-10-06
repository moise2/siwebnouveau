@extends('frontend.layouts.page', [
    'pageTitle' => $title,
    'pageId' => 'articles'
])

@section('page-content')
    <h2 class="card-title">{{ $title }}</h2>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

    <style>
        #map {
            width: 100%;
            height: 600px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            /* Ajout pour corriger le chevauchement */
            position: relative; /* Nécessaire pour que z-index fonctionne */
            z-index: 0; /* Assure que la carte reste en dessous d'autres éléments comme le menu */
        }
        .custom-popup {
            max-width: 300px;
        }
        .custom-popup h4 {
            color: #2c3e50;
            margin-bottom: 10px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }
        .custom-popup ul {
            padding-left: 15px;
            margin-top: 10px;
        }
        .custom-popup li {
            margin-bottom: 5px;
            color: #34495e;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .filter-section select {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
            margin-top: 5px;
        }
        .filter-section label {
            font-weight: 600;
            color: #2c3e50;
        }
        .stats-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 15px;
            margin-bottom: 20px;
        }
        .stats-title {
            color: #2c3e50;
            font-size: 1.1rem;
            margin-bottom: 15px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }
        .stats-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .stats-list li {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .stats-list li:last-child {
            border-bottom: none;
        }
    </style>

    <div class="container-fluid my-5">
        <div class="row">
            <!-- Filtres et Statistiques -->
            <div class="col-md-3">
                <div class="filter-section">
                    <div class="mb-4">
                        <label for="region-filter">Région</label>
                        <select id="region-filter" class="form-select">
                            <option value="">Toutes les régions</option>
                            @foreach($localites as $localite)
                                <option value="{{$localite->libelle}}">{{$localite->libelle}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="year-filter">Exercice</label>
                        <select id="year-filter" class="form-select">
                            <option value="">Tous les exercices</option>
                        </select>
                    </div>
                    <button id="reset-filters" class="btn btn-secondary">
                        Réinitialiser les filtres
                    </button>
                </div>

                <!-- Statistiques -->
                <div class="stats-card">
                    <h3 class="stats-title">Statistiques</h3>
                    <div id="statistics">
                        <!-- Les statistiques seront injectées ici -->
                    </div>
                </div>
            </div>
            
            <!-- Carte -->
            <div class="col-md-9">
                <div id="map"></div>
            </div>
        </div>

        <!-- Nouvelle rangée pour le tableau -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title mb-4">Liste des programmes</h3>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Programme</th>
                                        <th>Exercice</th>
                                        <th>Localité</th>
                                        <th>Type Localité</th>
                                    </tr>
                                </thead>
                                <tbody id="programmes-table-body">
                                    <!-- Les données seront injectées ici -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
    const initMap = () => {
        // Coordonnées centrées sur le Togo
        const togoCenter = [8.619543, 0.824782];
        
        // Création de la carte
        const map = L.map('map', {
            center: togoCenter,
            zoom: 7,
            minZoom: 6,
            maxZoom: 12
        });

        // Ajout d'une couche de tuiles OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        return map;
    };

    const regionCoordinates = {
        'Maritime': [6.1750, 1.2083],
        'Plateaux': [7.5400, 1.1300],
        'Centrale': [8.7000, 1.1333],
        'Kara': [9.5500, 1.1833],
        'Savanes': [10.8000, 0.4833]
    };

    const localiteMapping = {
        'Lomé-Commune': 'Maritime',
        'Agoè-Nyivé': 'Maritime',
        'Afagnan': 'Maritime',
        'Atakpamé': 'Plateaux',
        'Amlamé': 'Plateaux'
    };

    const createMarkers = (map, data) => {
        const markers = L.markerClusterGroup();
        
        // Grouper les programmes par localité
        const groupedData = data.reduce((acc, item) => {
            if (!acc[item.nom_localite]) {
                acc[item.nom_localite] = [];
            }
            acc[item.nom_localite].push(item);
            return acc;
        }, {});

        Object.entries(groupedData).forEach(([localite, programmes]) => {
            const region = localiteMapping[localite];
            const coordinates = region ? regionCoordinates[region] : [8.6195, 0.8248];

            const marker = L.marker(coordinates)
                .bindPopup(`
                    <div class="custom-popup">
                        <h4>${localite}</h4>
                        <p>Nombre de programmes: ${programmes.length}</p>
                        <ul>
                            ${programmes.map(p => `<li>${p.nom_programme}</li>`).join('')}
                        </ul>
                    </div>
                `);
            markers.addLayer(marker);
        });

        map.addLayer(markers);
        return markers;
    };

    const updateStatistics = (data) => {
        const stats = {
            totalProgrammes: data.length,
            programmesParRegion: {}
        };

        data.forEach(item => {
            const region = localiteMapping[item.nom_localite] || 'Autre';
            stats.programmesParRegion[region] = (stats.programmesParRegion[region] || 0) + 1;
        });

        document.getElementById('statistics').innerHTML = `
            <ul class="stats-list">
                <li><strong>Total des programmes:</strong> ${stats.totalProgrammes}</li>
                <li><strong>Par région:</strong></li>
                ${Object.entries(stats.programmesParRegion)
                    .map(([region, count]) => `<li>${region}: ${count}</li>`)
                    .join('')}
            </ul>
        `;

        // Mettre à jour le tableau
        updateTableData(data);
    };

    const updateTableData = (data) => {
        const tableBody = document.getElementById('programmes-table-body');
        tableBody.innerHTML = data.map(item => `
            <tr>
                <td><div class="truncate" title="${item.nom_programme}">${item.nom_programme}</div></td>
                <td>${item.exercice}</td>
                <td>${item.nom_localite}</td>
                <td>${item.type_localite}</td>
            </tr>
        `).join('');
    };

    const filterData = (data, map, currentMarkers) => {
        const selectedRegion = document.getElementById('region-filter').value;
        const selectedExercice = document.getElementById('year-filter').value;

        const filteredData = data.filter(item => {
            const matchRegion = !selectedRegion || localiteMapping[item.nom_localite] === selectedRegion;
            const matchExercice = !selectedExercice || item.exercice === selectedExercice;
            return matchRegion && matchExercice;
        });

        // Supprimer les marqueurs existants
        if (currentMarkers) {
            map.removeLayer(currentMarkers);
        }

        // Créer de nouveaux marqueurs avec les données filtrées
        const newMarkers = createMarkers(map, filteredData);
        
        // Mettre à jour les statistiques
        updateStatistics(filteredData);

        return newMarkers;
    };

    const resetFilters = (data, map, currentMarkers) => {
        // Reset select elements to default values
        document.getElementById('region-filter').value = '';
        document.getElementById('year-filter').value = '';

        // Remove existing markers
        if (currentMarkers) {
            map.removeLayer(currentMarkers);
        }

        // Create new markers with all data
        const newMarkers = createMarkers(map, data);
        
        // Update statistics with all data
        updateStatistics(data);

        return newMarkers;
    };

    const loadMapData = async () => {
        try {
            // Login first
            const loginResponse = await axios.post('/proxy/login', {
                username: 'dossul',
                password: 'P123456789++'
            });
            
            const token = loginResponse.data.access_token;

            // Get programmes data with token
            const response = await axios.get('/proxy/programmes-localites', {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            if (response.data.status === 'success') {
                const map = initMap();
                let markers = createMarkers(map, response.data.data);
                updateStatistics(response.data.data);

                initExerciceFilter(response.data.data);

                // Ajouter les écouteurs d'événements pour les filtres
                document.getElementById('region-filter').addEventListener('change', () => {
                    markers = filterData(response.data.data, map, markers);
                });
                
                document.getElementById('year-filter').addEventListener('change', () => {
                    markers = filterData(response.data.data, map, markers);
                });
                
                // Ajouter l'écouteur d'événements pour le bouton de réinitialisation
                document.getElementById('reset-filters').addEventListener('click', () => {
                    markers = resetFilters(response.data.data, map, markers);
                });
            }
        } catch (error) {
            console.error('Erreur lors du chargement des données:', error);
            if (error.response) {
                console.error('Détails de l\'erreur:', error.response.data);
            }
        }
    };

    // Initialize years in filter
    const initExerciceFilter = (data) => {
        const exercices = [...new Set(data.map(item => item.exercice))];
        const exerciceSelect = document.getElementById('year-filter');
        exerciceSelect.innerHTML = '<option value="">Tous les exercices</option>';
        
        exercices.forEach(exercice => {
            const option = document.createElement('option');
            option.value = exercice;
            option.textContent = exercice;
            exerciceSelect.appendChild(option);
        });
    };

    // Start everything when DOM is loaded
    document.addEventListener('DOMContentLoaded', () => {
        loadMapData();
    });
    </script>
@endsection
