<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Projets</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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
    </style>
</head>

<body>

    <div class="container">
        <!-- Profil utilisateur affiché en haut -->
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Bienvenue, John Doe</h2>
                <p>Vous êtes connecté en tant qu'admin</p>
            </div>
            <div>
                <button class="btn btn-danger">Déconnexion</button>
            </div>
        </div>

        <!-- Barre de recherche -->
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Rechercher un projet..." onkeyup="searchProjects()">
        </div>

        <!-- Liste des projets -->
        <div class="project-list" id="projectList">
            <!-- Les projets seront affichés ici -->
        </div>
    </div>

    <script>
        // Simulation des projets (données backend)
        const projects = [
            { id: 1, title: 'Projet de construction de routes', description: 'Amélioration des infrastructures routières dans la région.' },
            { id: 2, title: 'Programme d’éducation numérique', description: 'Mise en place de nouvelles salles informatiques dans les écoles.' },
            { id: 3, title: 'Projet d’énergie solaire', description: 'Installation de panneaux solaires pour alimenter les zones rurales.' },
            { id: 4, title: 'Projet d’agriculture durable', description: 'Introduction de nouvelles méthodes pour améliorer la production agricole.' },
            { id: 5, title: 'Amélioration des soins de santé', description: 'Renforcement des infrastructures des hôpitaux régionaux.' },
            { id: 6, title: 'Développement du tourisme local', description: 'Initiatives pour promouvoir les sites touristiques locaux.' }
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
                    <h4>${project.title}</h4>
                    <p>${project.description}</p>
                `;
                projectList.appendChild(projectItem);
            });
        }

        // Affiche tous les projets au chargement de la page
        displayProjects(projects);

        // Fonction de recherche dynamique
        function searchProjects() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const filteredProjects = projects.filter(project =>
                project.title.toLowerCase().includes(query) ||
                project.description.toLowerCase().includes(query)
            );
            displayProjects(filteredProjects);
        }
    </script>

</body>

</html>
