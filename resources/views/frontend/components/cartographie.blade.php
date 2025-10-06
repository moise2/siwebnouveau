<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

<style>
    #map {
        width: 100%;
        height: 600px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
</style>

<div class="container-fluid">
    <div class="row">
        <!-- Filtres -->
        <div class="col-md-3">
            <div class="filter-section">
                <div class="mb-4">
                    <label for="region-filter">Région</label>
                    <select id="region-filter" class="form-select">
                        <option value="">Toutes les régions</option>
                        <option value="Savanes">Savanes</option>
                        <option value="Kara">Kara</option>
                        <option value="Centrale">Centrale</option>
                        <option value="Plateaux">Plateaux</option>
                        <option value="Maritime">Maritime</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="year-filter">Année</label>
                    <select id="year-filter" class="form-select">
                        <option value="">Toutes les années</option>
                        <option value="2020">2020</option>
                        <option value="2021">2021</option>
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                    </select>
                </div>
            </div>
            <div id="statistics" class="mt-4">
                <!-- Les statistiques seront injectées ici -->
            </div>
        </div>
        
        <!-- Carte -->
        <div class="col-md-9">
            <div id="map"></div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
class ProgrammeMap {
    constructor() {
        this.regionCoordinates = {
            'Maritime': [6.1750, 1.2083],
            'Plateaux': [7.5400, 1.1300],
            'Centrale': [8.7000, 1.1333],
            'Kara': [9.5500, 1.1833],
            'Savanes': [10.8000, 0.4833]
        };
        
        this.localiteMapping = {
            'Lomé-Commune': 'Maritime',
            'Agoè-Nyivé': 'Maritime',
            'Afagnan': 'Maritime',
            'Atakpamé': 'Plateaux',
            'Amlamé': 'Plateaux'
        };
        
        this.markers = L.markerClusterGroup();
        this.initMap();
        this.initEventListeners();
    }

    initMap() {
        this.map = L.map('map').setView([8.6195, 0.8248], 7);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(this.map);
        this.map.addLayer(this.markers);
    }

    initEventListeners() {
        document.getElementById('region-filter').addEventListener('change', () => this.loadData());
        document.getElementById('year-filter').addEventListener('change', () => this.loadData());
    }

    async loadData() {
        try {
            const response = await axios.get('/proxy/getProgrammesLocalites');
            if (response.data.status === 'success') {
                this.displayData(response.data.data);
            }
        } catch (error) {
            console.error('Erreur lors du chargement des données:', error);
        }
    }

    displayData(data) {
        this.markers.clearLayers();
        
        const filteredData = this.filterData(data);
        const groupedData = this.groupDataByLocation(filteredData);
        
        Object.entries(groupedData).forEach(([location, programmes]) => {
            const coordinates = this.getLocationCoordinates(location);
            const marker = this.createMarker(location, programmes, coordinates);
            this.markers.addLayer(marker);
        });
        
        this.updateStatistics(filteredData);
    }

    filterData(data) {
        const region = document.getElementById('region-filter').value;
        const year = document.getElementById('year-filter').value;
        
        return data.filter(item => {
            const matchRegion = !region || this.localiteMapping[item.nom_localite] === region;
            const matchYear = !year || item.annee === year;
            return matchRegion && matchYear;
        });
    }

    groupDataByLocation(data) {
        return data.reduce((acc, item) => {
            if (!acc[item.nom_localite]) acc[item.nom_localite] = [];
            acc[item.nom_localite].push(item);
            return acc;
        }, {});
    }

    getLocationCoordinates(location) {
        const region = this.localiteMapping[location];
        return region ? this.regionCoordinates[region] : [8.6195, 0.8248];
    }

    createMarker(location, programmes, coordinates) {
        return L.marker(coordinates)
            .bindPopup(`
                <div class="custom-popup">
                    <h4>${location}</h4>
                    <p>Nombre de programmes: ${programmes.length}</p>
                    <ul>
                        ${programmes.map(p => `<li>${p.nom_programme}</li>`).join('')}
                    </ul>
                </div>
            `);
    }

    updateStatistics(data) {
        const stats = {
            totalProgrammes: data.length,
            programmesParRegion: {}
        };

        data.forEach(item => {
            const region = this.localiteMapping[item.nom_localite] || 'Autre';
            stats.programmesParRegion[region] = (stats.programmesParRegion[region] || 0) + 1;
        });

        document.getElementById('statistics').innerHTML = `
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Statistiques</h5>
                    <p>Total des programmes: ${stats.totalProgrammes}</p>
                    <h6>Par région:</h6>
                    <ul>
                        ${Object.entries(stats.programmesParRegion)
                            .map(([region, count]) => `<li>${region}: ${count}</li>`)
                            .join('')}
                    </ul>
                </div>
            </div>
        `;
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    const programmeMap = new ProgrammeMap();
    programmeMap.loadData();
});
</script>
