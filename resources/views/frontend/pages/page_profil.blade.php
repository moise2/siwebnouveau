@extends('frontend.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar" style="min-width: 220px;">
            <div class="sidebar-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active text-light" href="#">
                            <i class="fas fa-tachometer-alt"></i> Tableau de Bord
                        </a>
                    </li>
                    <li class="nav-item">
                        @if(Auth::guard('utilisateur')->check())
                            @if(Auth::guard('utilisateur')->user()->profile_image)
                                <img src="{{ asset('storage/' . Auth::guard('utilisateur')->user()->profile_image) }}" alt="Profil" class="rounded-circle" style="width: 60px; height: 60px;">
                            @else
                                <i class="fas fa-user-circle" style="font-size: 60px; color: white;"></i>
                            @endif
                            <h5 class="mt-2 text-light">
                                {{ Auth::guard('utilisateur')->user()->prenoms }} {{ Auth::guard('utilisateur')->user()->nom }}
                            </h5>
                        @else
                            <h5 class="mt-2">Utilisateur non connecté</h5>
                        @endif
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="GET" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2 text-primary">Tableau de Bord</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button class="btn btn-outline-info me-2" id="refreshBtn">
                        <i class="fas fa-sync-alt"></i> Actualiser
                    </button>
                </div>
            </div>


            <!-- Overview Cards -->
            <div class="row mb-4">
                <div class="col-sm-6 col-md-3">
                    <div class="card border-light rounded shadow-lg">
                        <div class="card-body text-center">
                            <h5 class="card-title text-success">Total des projets</h5>
                            <p class="card-text fs-3 fw-bold">{{ $projets->count() }}</p>
                            <a href="#" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#projetsModal">Voir Détails</a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="card border-light rounded shadow-lg">
                        <div class="card-body text-center">
                            <h5 class="card-title text-info">Totals des programmes</h5>
                            <p class="card-text fs-3 fw-bold">{{ $programmes->count() }}</p>
                            <a href="#" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#programmesModal">Voir Détails</a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="card border-light rounded shadow-lg">
                        <div class="card-body text-center">
                            <h5 class="card-title text-warning">Projets en Cours</h5>
                            <p class="card-text fs-3 fw-bold">{{ $projets->where('status', 'en cours')->count() }}</p>
                            <a href="#" class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#projetsEnCoursModal">Voir Détails</a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="card border-light rounded shadow-lg">
                        <div class="card-body text-center">
                            <h5 class="card-title text-warning">Programmes en Cours</h5>
                            <p class="card-text fs-3 fw-bold">{{ $programmes->where('status', 'en cours')->count() }}</p>
                            <a href="#" class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#programmesEnCoursModal">Voir Détails</a>
                        </div>
                    </div>
                </div>
            </div>

<!-- Modal for Projets Details -->
    <div class="modal fade" id="projetsModal" tabindex="-1" aria-labelledby="projetsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="projetsModalLabel">Détails des Projets</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <!-- Include jsPDF library -->
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

                <div class="modal-body">
                        <h6 style="font-size: 14px; margin-bottom: 10px;">Liste des Projets</h6>
                        <ul id="projectList" style="padding: 0;">
                            @foreach($projets as $projet)
                                <li class="project-item" style="{{ $projet->projetlocalite->count() >= 2 ? 'display: none;' : '' }}; font-size: 12px; margin-bottom: 15px;">
                                    <p style="background-color: green; color: white; padding: 5px; margin: 0;"><strong>{{ optional($projet->institution)->nom ?? 'Institution non spécifiée' }}</strong></p>
                                    <p style="font-weight: bold; margin: 5px 0;">{{ $projet->nom }}</p>
                                    <p style="margin: 2px 0;">Lieu d'exécution: 
                                    @foreach($projet->projetlocalite as $localite)
                                         {{ $localite->localite->libelle }}
                                    @endforeach
                                    </p>
                                    <p style="margin: 2px 0;">Budget: {{ $projet->budget }} FCFA</p>
                                    <p style="margin: 2px 0;">État du projet: {{ $projet->etat_projet }}</p>
                                    <p style="margin: 2px 0;">Taux d'exécution physique: {{ $projet->taux_execution_physique }}%</p>
                                    <p style="margin: 2px 0;">Taux d'exécution financier: {{ $projet->taux_execution_financier }}%</p>
                                    @if(!$loop->last)
                                        <hr style="border-top: 1px solid #ccc; margin: 10px 0;">
                                    @endif
                                </li>
                            @endforeach
                        </ul>

                        @if(count($projets) > 2)
                            <button id="showMoreBtn" class="btn btn-primary w-100" style="font-size: 12px;">Voir Plus</button>
                        @endif

                        <button id="downloadBtn" class="btn btn-success w-100" style="font-size: 12px; margin-top: 10px;">Télécharger Détails</button>
                </div>

                        <script>
                            // Show more button functionality
                            document.getElementById('showMoreBtn')?.addEventListener('click', function () {
                                const hiddenItems = document.querySelectorAll('.project-item[style*="display: none"]');
                                hiddenItems.forEach(item => item.style.display = 'list-item');
                                this.style.display = 'none'; // Hide the 'Voir Plus' button after showing all items
                            });

                            // Download details as PDF functionality
                            document.getElementById('downloadBtn').addEventListener('click', function () {
                                const { jsPDF } = window.jspdf;
                                const doc = new jsPDF();

                                let yOffset = 10; // Starting y position for text in PDF

                                doc.setFontSize(12);
                                doc.text("Liste des Projets", 10, yOffset);
                                yOffset += 10;

                                document.querySelectorAll('.project-item').forEach(item => {
                                    doc.setFontSize(10);
                                    doc.text(item.innerText, 10, yOffset);
                                    yOffset += 10 + (item.innerText.split("\n").length * 5);

                                    doc.line(10, yOffset, 200, yOffset); // Separator line
                                    yOffset += 5;

                                    // Add a new page if content exceeds the page height
                                    if (yOffset > 270) {
                                        doc.addPage();
                                        yOffset = 10;
                                    }
                                });

                                doc.save('projets_details.pdf'); // Save the PDF file
                            });
                        </script>


                        <script>
                            // Show more button functionality
                            document.getElementById('showMoreBtn')?.addEventListener('click', function () {
                                const hiddenItems = document.querySelectorAll('.project-item[style*="display: none"]');
                                hiddenItems.forEach(item => item.style.display = 'list-item');
                                this.style.display = 'none'; // Hide the 'Voir Plus' button after showing all items
                            });

                            // Download details functionality
                            document.getElementById('downloadBtn').addEventListener('click', function () {
                                let text = "Liste des Projets\n\n";
                                
                                document.querySelectorAll('.project-item').forEach(item => {
                                    text += item.innerText + "\n\n";
                                });
                                
                                const blob = new Blob([text], { type: 'text/plain' });
                                const url = URL.createObjectURL(blob);
                                const a = document.createElement('a');
                                a.href = url;
                                a.download = 'projets_details.txt';
                                document.body.appendChild(a);
                                a.click();
                                document.body.removeChild(a);
                                URL.revokeObjectURL(url);
                            });
                        </script>

            </div>
        </div>
    </div>

<!-- Modal for Programmes Details -->
<!-- Include jsPDF library -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

        <div class="modal fade" id="programmesModal" tabindex="-1" aria-labelledby="programmesModalLabel" aria-hidden="true">
            <div class="modal-dialog">
            <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="projetsModalLabel">Détails des Projets</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <!-- Include jsPDF library -->
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

                <div class="modal-body">
                        <h6 style="font-size: 14px; margin-bottom: 10px;">Liste des Projets</h6>
                        <ul id="projectList" style="padding: 0;">
                            @foreach($programmes as $projet)
                                <li class="project-item" style="{{ $projet->programmelocalite->count() >= 2 ? 'display: none;' : '' }}; font-size: 12px; margin-bottom: 15px;">
                                    <p style="background-color: green; color: white; padding: 5px; margin: 0;"><strong>{{ optional($projet->institution)->nom ?? 'Institution non spécifiée' }}</strong></p>
                                    <p style="font-weight: bold; margin: 5px 0;">{{ $projet->nom }}</p>
                                    <p style="margin: 2px 0;">Lieu d'exécution: 
                                    @foreach($projet->programmelocalite as $localite)
                                         {{ $localite->localite->libelle }}
                                    @endforeach
                                    </p>
                                    <p style="margin: 2px 0;">Budget: {{ $projet->budget }} FCFA</p>
                                    <p style="margin: 2px 0;">État du projet: {{ $projet->etat_projet }}</p>
                                    <p style="margin: 2px 0;">Taux d'exécution physique: {{ $projet->taux_execution_physique }}%</p>
                                    <p style="margin: 2px 0;">Taux d'exécution financier: {{ $projet->taux_execution_financier }}%</p>
                                    @if(!$loop->last)
                                        <hr style="border-top: 1px solid #ccc; margin: 10px 0;">
                                    @endif
                                </li>
                            @endforeach
                        </ul>

                        @if(count($projets) > 2)
                            <button id="showMoreBtn" class="btn btn-primary w-100" style="font-size: 12px;">Voir Plus</button>
                        @endif

                        <button id="downloadBtn" class="btn btn-success w-100" style="font-size: 12px; margin-top: 10px;">Télécharger Détails</button>
                </div>

                        <script>
                            // Show more button functionality
                            document.getElementById('showMoreBtn')?.addEventListener('click', function () {
                                const hiddenItems = document.querySelectorAll('.project-item[style*="display: none"]');
                                hiddenItems.forEach(item => item.style.display = 'list-item');
                                this.style.display = 'none'; // Hide the 'Voir Plus' button after showing all items
                            });

                            // Download details as PDF functionality
                            document.getElementById('downloadBtn').addEventListener('click', function () {
                                const { jsPDF } = window.jspdf;
                                const doc = new jsPDF();

                                let yOffset = 10; // Starting y position for text in PDF

                                doc.setFontSize(12);
                                doc.text("Liste des Projets", 10, yOffset);
                                yOffset += 10;

                                document.querySelectorAll('.project-item').forEach(item => {
                                    doc.setFontSize(10);
                                    doc.text(item.innerText, 10, yOffset);
                                    yOffset += 10 + (item.innerText.split("\n").length * 5);

                                    doc.line(10, yOffset, 200, yOffset); // Separator line
                                    yOffset += 5;

                                    // Add a new page if content exceeds the page height
                                    if (yOffset > 270) {
                                        doc.addPage();
                                        yOffset = 10;
                                    }
                                });

                                doc.save('projets_details.pdf'); // Save the PDF file
                            });
                        </script>


                        <script>
                            // Show more button functionality
                            document.getElementById('showMoreBtn')?.addEventListener('click', function () {
                                const hiddenItems = document.querySelectorAll('.project-item[style*="display: none"]');
                                hiddenItems.forEach(item => item.style.display = 'list-item');
                                this.style.display = 'none'; // Hide the 'Voir Plus' button after showing all items
                            });

                            // Download details functionality
                            document.getElementById('downloadBtn').addEventListener('click', function () {
                                let text = "Liste des Projets\n\n";
                                
                                document.querySelectorAll('.project-item').forEach(item => {
                                    text += item.innerText + "\n\n";
                                });
                                
                                const blob = new Blob([text], { type: 'text/plain' });
                                const url = URL.createObjectURL(blob);
                                const a = document.createElement('a');
                                a.href = url;
                                a.download = 'projets_details.txt';
                                document.body.appendChild(a);
                                a.click();
                                document.body.removeChild(a);
                                URL.revokeObjectURL(url);
                            });
                        </script>

            </div>
            </div>
        </div>

        <script>
            // Download details as PDF functionality
            document.getElementById('downloadProgramBtn').addEventListener('click', function () {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();

                let yOffset = 10; // Starting y position for text in PDF

                doc.setFontSize(12);
                doc.text("Liste des Programmes", 10, yOffset);
                yOffset += 10;

                document.querySelectorAll('.programme-item').forEach(item => {
                    doc.setFontSize(10);
                    doc.text(item.innerText, 10, yOffset);
                    yOffset += 10 + (item.innerText.split("\n").length * 5);

                    doc.line(10, yOffset, 200, yOffset); // Separator line
                    yOffset += 5;

                    // Add a new page if content exceeds the page height
                    if (yOffset > 270) {
                        doc.addPage();
                        yOffset = 10;
                    }
                });

                doc.save('programmes_details.pdf'); // Save the PDF file
            });
        </script>

<!-- Modal for Projets en Cours Details -->
<!-- Include jsPDF library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<div class="modal fade" id="projetsEnCoursModal" tabindex="-1" aria-labelledby="projetsEnCoursModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="projetsEnCoursModalLabel">Projets en Cours</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 style="font-size: 14px; margin-bottom: 10px;">Projets en cours</h6>
                <ul id="projetsEnCoursList" style="padding: 0;">
                    @foreach($projets->where('status', 'en cours') as $projetEnCours)
                        <li class="projet-en-cours-item" style="font-size: 12px; margin-bottom: 15px;">
                            <strong>{{ $projetEnCours->nom }}</strong>
                            <p style="margin: 2px 0;">Lieu d'exécution: 
                                @foreach($projetEnCours->projetlocalite as $localite)
                                    {{ $localite->localite->libelle }}
                                @endforeach
                            </p>
                            <p style="margin: 2px 0;">Taux d'exécution physique: {{ $projetEnCours->taux_execution_physique }}%</p>
                            <p style="margin: 2px 0;">Taux d'exécution financier: {{ $projetEnCours->taux_execution_financier }}%</p>
                        </li>
                        <hr style="border-top: 1px solid #ccc; margin: 10px 0;">
                    @endforeach
                </ul>
                
                <button id="downloadProjetsEnCoursBtn" class="btn btn-success w-100">Télécharger Détails</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Download "Projets en Cours" details as PDF functionality
    document.getElementById('downloadProjetsEnCoursBtn').addEventListener('click', function () {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        let yOffset = 10; // Starting y position for text in PDF

        doc.setFontSize(12);
        doc.text("Projets en cours", 10, yOffset);
        yOffset += 10;

        document.querySelectorAll('.projet-en-cours-item').forEach(item => {
            doc.setFontSize(10);
            doc.text(item.innerText, 10, yOffset);
            yOffset += 10 + (item.innerText.split("\n").length * 5);

            doc.line(10, yOffset, 200, yOffset); // Separator line
            yOffset += 5;

            // Add a new page if content exceeds the page height
            if (yOffset > 270) {
                doc.addPage();
                yOffset = 10;
            }
        });

        doc.save('projets_en_cours_details.pdf'); // Save the PDF file
    });
</script>


<!-- Modal for Programmes en Cours Details -->
<!-- Include jsPDF library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<div class="modal fade" id="programmesEnCoursModal" tabindex="-1" aria-labelledby="programmesEnCoursModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="programmesEnCoursModalLabel">Programmes en Cours</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 style="font-size: 14px; margin-bottom: 10px;">Programmes en cours</h6>
                <ul id="programmesEnCoursList" style="padding: 0;">
                    @foreach($programmes->where('status', 'en cours') as $programmeEnCours)
                        <li class="programme-en-cours-item" style="font-size: 12px; margin-bottom: 15px;">
                            <strong>{{ $programmeEnCours->nom }}</strong>
                            <p style="margin: 2px 0;">Lieu d'exécution: 
                                @foreach($programmeEnCours->programmelocalite as $localite)
                                    {{ $localite->localite->libelle }}
                                @endforeach
                            </p>
                            <p style="margin: 2px 0;">Taux d'exécution physique: {{ $programmeEnCours->taux_execution_physique }}%</p>
                            <p style="margin: 2px 0;">Taux d'exécution financier: {{ $programmeEnCours->taux_execution_financier }}%</p>
                        </li>
                        <hr style="border-top: 1px solid #ccc; margin: 10px 0;">
                    @endforeach
                </ul>
                
                <button id="downloadProgrammesEnCoursBtn" class="btn btn-success w-100">Télécharger Détails</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Download "Programmes en Cours" details as PDF functionality
    document.getElementById('downloadProgrammesEnCoursBtn').addEventListener('click', function () {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        let yOffset = 10; // Starting y position for text in PDF

        doc.setFontSize(12);
        doc.text("Programmes en cours", 10, yOffset);
        yOffset += 10;

        document.querySelectorAll('.programme-en-cours-item').forEach(item => {
            doc.setFontSize(10);
            doc.text(item.innerText, 10, yOffset);
            yOffset += 10 + (item.innerText.split("\n").length * 5);

            doc.line(10, yOffset, 200, yOffset); // Separator line
            yOffset += 5;

            // Add a new page if content exceeds the page height
            if (yOffset > 270) {
                doc.addPage();
                yOffset = 10;
            }
        });

        doc.save('programmes_en_cours_details.pdf'); // Save the PDF file
    });
</script>



            <!-- Execution and Program Rates -->
<!-- Sélection de l'année -->
<div class="row mb-4">
    <div class="col-md-4">
        <label for="yearSelect" class="form-label">Sélectionner l'année</label>
        <select id="yearSelect" class="form-select">
            <option value="">Toutes les années</option>
            @for ($year =explode('-',now())[0];$year>(explode('-',now())[0]-10);$year-- )
                <option value="{{ $year }}" @if ($year == explode('-',now())[0]) selected @endif>
                    {{ $year }}
                </option>
            @endfor

        </select>
    </div>
</div>

<div class="container mt-5">
    <div class="row mb-4">
        <!-- Graphique pour les projets -->
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    Taux d'Exécution des Projets par Institution
                </div>
                <div class="card-body">
                    <canvas id="projectsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Graphique pour les programmes -->
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white">
                    Taux d'Exécution des Programmes par Institution
                </div>
                <div class="card-body">
                    <canvas id="programsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const projectsData = @json($projets);
    const programsData = @json($programmes);
    //console.log(projectsData);

    // Fonction pour obtenir les couleurs dynamiquement
    function getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    // Fonction pour structurer les données par institution
    function structureDataByInstitution(data, year) {
        const structuredData = {};
        //console.log(data);
        data.forEach(item => {
            const institution = item.institution.libelle;
            if (item.date_fin != null && (year === "" || item.date_fin.includes(year))) {
                if (!structuredData[institution]) {
                    structuredData[institution] = { years: [], physique: [], financier: [] };
                }
                structuredData[institution].years.push(item.date_fin);
                structuredData[institution].physique.push(item.taux_execution_physique);
                structuredData[institution].financier.push(item.taux_execution_financier);
            }
        });
        return structuredData;
    }

    // Fonction pour mettre à jour les graphiques
    function updateCharts(year) {
        
        // Structurer les données par institution
        const projectsByInstitution = structureDataByInstitution(projectsData, year);
        const programsByInstitution = structureDataByInstitution(programsData, year);

        // Préparer les datasets pour chaque institution
        projectsChart.data.labels = [];
        projectsChart.data.datasets = [];
        Object.keys(projectsByInstitution).forEach(institution => {
            const institutionData = projectsByInstitution[institution];
            projectsChart.data.labels = institutionData.years;
            projectsChart.data.datasets.push({
                label: `Physique - ${institutionData.physique}%`,
                data: institutionData.physique,
                borderColor: getRandomColor(),
                fill: false,
                tension: 0.1
            });
            projectsChart.data.datasets.push({
                label: `Financier - ${institutionData.financier}%`,
                data: institutionData.financier,
                borderColor: getRandomColor(),
                fill: false,
                tension: 0.1
            });
        });
        projectsChart.update();

        // Préparer les datasets pour chaque institution dans les programmes
        programsChart.data.labels = [];
        programsChart.data.datasets = [];
        console.log(programsByInstitution);
        Object.keys(programsByInstitution).forEach(institution => {
            const institutionData = programsByInstitution[institution];
            programsChart.data.labels = institutionData.years;
            programsChart.data.datasets.push({
                label: `Physique - ${institutionData.physique}%`,
                data: institutionData.physique,
                borderColor: getRandomColor(),
                fill: false,
                tension: 0.1
            });
            programsChart.data.datasets.push({
                label: `Financier - ${institutionData.financier}%`,
                data: institutionData.financier,
                borderColor: getRandomColor(),
                fill: false,
                tension: 0.1
            });
        });
        programsChart.update();
    }

    // Création des graphiques avec Chart.js
    const projectsChart = new Chart(document.getElementById('projectsChart'), {
        type: 'line',
        data: {
            labels: [],
            datasets: []
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const programsChart = new Chart(document.getElementById('programsChart'), {
        type: 'line',
        data: {
            labels: [],
            datasets: []
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Écouteur pour la sélection de l'année
    document.getElementById('yearSelect').addEventListener('change', function() {
        const selectedYear = this.value;
        updateCharts(selectedYear);
    });

    // Charger les graphiques avec l'année par défaut
    document.addEventListener('DOMContentLoaded', () => {
        const defaultYear = document.getElementById('yearSelect').value;
        updateCharts(defaultYear);
    });
</script>






            <!-- Projets and Programmes Details Table -->
<style>
    /* Réduction de la taille des polices pour les tableaux */
    .table-sm th,
    .table-sm td {
        font-size: 0.875rem; /* Taille légèrement plus petite que la taille par défaut */
    }
    .section-title {
        font-size: 1rem; /* Taille des titres des sections Projets et Programmes */
        font-weight: bold;
    }
</style>

<div class="card mb-4 shadow-lg">
    <div class="card-header bg-light">
        <h5 class="card-title">Détails des Projets & Programmes</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Tableau pour les Projets -->
            <div class="col-md-6">
                <h6 class="section-title text-center">Projets</h6>
                <table class="table table-hover table-bordered table-sm">
                    <thead>
                        <tr>
                            <th style="font-size: 12px;">Nom du Projet</th>
                            <th style="font-size: 12px;">Statut</th>
                            <th style="font-size: 12px;">Date Début</th>
                            <th style="font-size: 12px;">Date Fin</th>
                            <th style="font-size: 12px;">Lieu d'Exécution</th>
                            <th style="font-size: 12px;">Taux Physique</th>
                            <th style="font-size: 12px;">Taux Financier</th>
                            <!-- <th>Actions</th> -->
                        </tr>
                    </thead>
                    <tbody id="projectDetails">
                        @foreach($projets as $projet)
                        <tr data-year="{{ $projet->annee }}">
                            <td style="width: 150px; font-size:small">{{ $projet->nom }}</td>
                            <!-- <td>{{ $projet->annee }}</td> -->
                            <td style="width: 70px; font-size:small">{{ $projet->etat_projet }}</td>
                            <td style="width: 70px; font-size:small">{{ \Carbon\Carbon::parse($projet->date_debut)->format('m/Y') }}</td>
                            <td style="width: 70px; font-size:small">{{ \Carbon\Carbon::parse($projet->date_fin)->format('m/Y') }}</td>
                            <td style="width: 70px; font-size:small">
                                    @foreach($projet->projetlocalite as $localite)
                                         {{ $localite->localite->libelle }}
                                    @endforeach
                            </td>
                            <td style="width: 70px; font-size:small">{{ $projet->taux_execution_physique }}%</td>
                            <td style="width: 70px; font-size:small">{{ $projet->taux_execution_financier }}%</td>
                            <!-- <td><a href="#" class="btn btn-info btn-sm">Détails</a></td> -->
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Tableau pour les Programmes -->
            <div class="col-md-6">
                <h6 class="section-title text-center">Programmes</h6>
                <table class="table table-hover table-bordered table-sm">
                    <thead>
                        <tr>
                            <th style="width: 150px; font-size:small">Nom du Programme</th>
                            <!-- <th>Année</th> -->
                            <th style="width: 70px; font-size:small">Statut</th>
                            <th  style="width: 70px; font-size:small">Date Début</th>
                            <th style="width: 70px; font-size:small">Date Fin</th>
                            <th style="width: 70px; font-size:small">Lieu d'Exécution</th>
                            <th style="width: 70px; font-size:small">Taux Physique</th>
                            <th style="width: 70px; font-size:small">Taux Financier</th>
                            <!-- <th>Actions</th> -->
                        </tr>
                    </thead>
                    <tbody id="programmeDetails">
                        @foreach($programmes as $programme)
                        <tr data-year="{{ $programme->annee }}">
                            <td style="width: 150px; font-size:small">{{ $programme->nom }}</td>
                            <!-- <td>{{ $programme->annee }}</td> -->
                            <td style="width: 70px; font-size:small">{{ $programme->etat_programme }}</td>
                            <td style="width: 70px; font-size:small">{{ \Carbon\Carbon::parse($programme->date_debut)->format('m/Y') }}</td>
                            <td style="width: 70px; font-size:small">{{ \Carbon\Carbon::parse($programme->date_fin)->format('m/Y') }}</td>
                            <td style="width: 70px; font-size:small"> 
                               @foreach($programme->programmelocalite as $localite)
                                    {{ $localite->localite->libelle }}
                               @endforeach
                            </td>
                            <td style="width: 70px; font-size:small">{{ $programme->taux_execution_physique }}%</td>
                            <td style="width: 70px; font-size:small">{{ $programme->taux_execution_financier }}%</td>
                            <!-- <td><a href="#" class="btn btn-info btn-sm">Détails</a></td> -->
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

        </main>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@endpush
@endsection
