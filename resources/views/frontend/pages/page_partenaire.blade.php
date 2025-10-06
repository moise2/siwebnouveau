<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Projets avec Synthèse et Graphiques</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js CDN -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
        }

        .container {
            margin-top: 30px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }

        .search-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-container input {
            width: 100%;
            max-width: 600px;
            padding: 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
        }

        .project-list {
            margin-top: 20px;
        }

        .project-item {
            padding: 15px;
            border-bottom: 1px solid #ccc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .project-item:last-child {
            border-bottom: none;
        }

        .project-item h4 {
            margin: 0;
            font-size: 1.5rem;
            color: #333;
        }

        .project-item p {
            margin: 0;
            color: #777;
        }

        .synthese-container {
            margin-top: 20px;
            padding: 20px;
            background-color: #eee;
            border-radius: 10px;
        }

        .chart-container {
            margin-top: 20px;
        }

        canvas {
            max-width: 100%;
            height: 400px;
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Profil utilisateur affiché en haut -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Bienvenue, John Doe</h2>
                <p>Vous êtes connecté en tant qu'admin</p>
            </div>
            <div>
                <button class="btn btn-danger">Déconnexion</button>
            </div>
        </div>

        <!-- Onglets horizontaux -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="projets-tab" data-bs-toggle="tab" data-bs-target="#projets" type="button" role="tab" aria-controls="projets" aria-selected="true">Projets</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="synthese-tab" data-bs-toggle="tab" data-bs-target="#synthese" type="button" role="tab" aria-controls="synthese" aria-selected="false">Synthèse</button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- Onglet Liste des Projets -->
            <div class="tab-pane fade show active" id="projets" role="tabpanel" aria-labelledby="projets-tab">
                <!-- Barre de recherche -->
                <div class="search-container mt-4">
                    <input type="text" id="searchInput" placeholder="Rechercher un projet..." onkeyup="searchProjects()">
                </div>

                <!-- Liste des projets -->
                <div class="project-list" id="projectList">
                    <!-- Les projets seront affichés ici -->
                </div>
            </div>

            <!-- Onglet Synthèse -->
            <div class="tab-pane fade" id="synthese" role="tabpanel" aria-labelledby="synthese-tab">
                <div class="synthese-container mt-4">
                    <h4>Synthèse des Projets</h4>
                    <div>
                        <p>Total des projets : <span id="totalProjects">0</span></p>
                        <p>Projets en cours : <span id="ongoingProjects">0</span></p>
                        <p>Projets terminés : <span id="completedProjects">0</span></p>
                    </div>

                    <!-- Graphiques -->
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modale pour afficher les détails d'un projet -->
        <div class="modal fade" id="projectDetailsModal" tabindex="-1" aria-labelledby="projectDetailsLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="projectDetailsLabel">Détails du Projet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Contenu dynamique des détails du projet -->
                        <h4 id="projectTitle"></h4>
                        <p id="projectDescription"></p>
                        <p><strong>Statut :</strong> <span id="projectStatus"></span></p>
                        <p><strong>Progression :</strong> <span id="projectProgress"></span>%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Simulation des projets (données backend)
        const projects = [
            { id: 1, title: 'Projet de construction de routes', description: 'Amélioration des infrastructures routières dans la région.', status: 'En cours', progress: 50 },
            { id: 2, title: 'Programme d’éducation numérique', description: 'Mise en place de nouvelles salles informatiques dans les écoles.', status: 'Terminé', progress: 100 },
            { id: 3, title: 'Projet d’énergie solaire', description: 'Installation de panneaux solaires pour alimenter les zones rurales.', status: 'En cours', progress: 75 },
            { id: 4, title: 'Projet d’agriculture durable', description: 'Introduction de nouvelles méthodes pour améliorer la production agricole.', status: 'En cours', progress: 40 },
            { id: 5, title: 'Amélioration des soins de santé', description: 'Renforcement des infrastructures des hôpitaux régionaux.', status: 'Terminé', progress: 100 },
            { id: 6, title: 'Développement du tourisme local', description: 'Initiatives pour promouvoir les sites touristiques locaux.', status: 'En cours', progress: 60 }
        ];

        // Fonction pour afficher les projets
        function displayProjects(filteredProjects) {
            const projectList = document.getElementById('projectList');
            projectList.innerHTML = '';  // Vide la liste actuelle

            if (filteredProjects.length === 0) {
                projectList.innerHTML = '<p>Aucun projet trouvé.</p>';
                return;
            }

            filteredProjects.forEach(project => {
                const projectItem = document.createElement('div');
                projectItem.classList.add('project-item');
                projectItem.innerHTML = `
                    <div>
                        <h4>${project.title}</h4>
                        <p>${project.description}</p>
                    </div>
                    <div>
                        <button class="btn btn-primary" onclick="viewProjectDetails(${project.id})">Voir Détails</button>
                    </div>
                `;
                projectList.appendChild(projectItem);
            });

            // Mettre à jour la synthèse
            updateSynthese(filteredProjects);
        }

        // Fonction de recherche dynamique
        function searchProjects() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const filteredProjects = projects.filter(project =>
                project.title.toLowerCase().includes(searchInput) || project.description.toLowerCase().includes(searchInput)
            );
            displayProjects(filteredProjects);
        }

        // Fonction pour afficher les détails d'un projet
        function viewProjectDetails(projectId) {
            const project = projects.find(p => p.id === projectId);
            document.getElementById('projectTitle').textContent = project.title;
            document.getElementById('projectDescription').textContent = project.description;
            document.getElementById('projectStatus').textContent = project.status;
            document.getElementById('projectProgress').textContent = project.progress;

            const modal = new bootstrap.Modal(document.getElementById('projectDetailsModal'));
            modal.show();
        }

        // Fonction pour mettre à jour la synthèse
        function updateSynthese(filteredProjects) {
            const totalProjects = filteredProjects.length;
            const ongoingProjects = filteredProjects.filter(project => project.status === 'En cours').length;
            const completedProjects = filteredProjects.filter(project => project.status === 'Terminé').length;

            document.getElementById('totalProjects').textContent = totalProjects;
            document.getElementById('ongoingProjects').textContent = ongoingProjects;
            document.getElementById('completedProjects').textContent = completedProjects;

            // Mettre à jour les graphiques
            updateCharts(ongoingProjects, completedProjects);
        }

        // Fonction pour initialiser et mettre à jour les graphiques
        function updateCharts(ongoing, completed) {
            const ctx = document.getElementById('statusChart').getContext('2d');

            // Si le graphique existe déjà, le détruire avant de le recréer
            if (window.statusChart) {
                window.statusChart.destroy();
            }

            // Créer un nouveau graphique
            window.statusChart = new Chart(ctx, {
                type: 'pie', // Type de graphique (pie chart pour une synthèse)
                data: {
                    labels: ['Projets en cours', 'Projets terminés'],
                    datasets: [{
                        data: [ongoing, completed],
                        backgroundColor: ['#f39c12', '#2ecc71'], // Couleurs personnalisées
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        }

        // Initialiser les projets affichés
        window.onload = function() {
            displayProjects(projects);  // Afficher tous les projets au début
        };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
