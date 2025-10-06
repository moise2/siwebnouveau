<body>
    <!-- Profil utilisateur flottant et déplaçable -->
    <div class="user-profile" id="userProfile">
        <img src="https://via.placeholder.com/50" alt="Photo de profil">
        <div class="user-info">
            <h4>John Doe</h4>
            <p>Admin</p>
        </div>
        <div class="user-actions">
            <i class="fas fa-cog" title="Paramètres" onclick="toggleDropdown(event)"></i>
            <i class="fas fa-user" title="Options" onclick="toggleDropdown(event)"></i>
            <div class="dropdown-menu" id="dropdownMenu" style="display: none;">
                <a href="#">Paramètres</a>
                <a href="#">Déconnexion</a>
            </div>
        </div>
    </div>


    <!-- Contenu principal -->
    <div class="container">
        <!-- Titre du tableau de bord -->
        {{-- <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Bienvenue, John Doe</h2>
                <p>Vous êtes connecté en tant qu'admin</p>
            </div>
            <div>
                <button class="btn btn-danger">Déconnexion</button>
            </div>
        </div> --}}

        <div class="dashboard-header">
            <h1>Tableau de Bord - Suivi des Réformes</h1>
        </div>

        <!-- Section des chiffres clés -->
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <h2 class="counter" id="totalProjects">0</h2>
                    <p>Total de Projets</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <h2 class="counter" id="totalPrograms">0</h2>
                    <p>Total de Programmes</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <h2 class="counter" id="totalFunds">0</h2>
                    <p>Fonds Totaux (en millions)</p>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="text-center mt-5">
            <a href="#" id="voirProjets" class="btn btn-primary">Voir les Projets</a>
            <a href="#" class="btn btn-primary">Voir les Programmes</a>
            <a href="#" class="btn btn-primary">Suivi des Fonds</a>
        </div>

        <div id="tabsContainer" style="display: none;">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="data-tab" data-toggle="tab" href="#data" role="tab" aria-controls="data" aria-selected="true">Données</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="graphs-tab" data-toggle="tab" href="#graphs" role="tab" aria-controls="graphs" aria-selected="false">Graphiques</a>
                </li>
            </ul>
            <br>
            <br>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="data" role="tabpanel" aria-labelledby="data-tab">
                    <input type="text" id="searchInput" placeholder="Recherche..." class="form-control" style="margin-top: 10px;">
                    <table class="table table-striped" id="dataTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Détails</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Projet Alpha Projet Alpha Projet Alpha Projet Alpha</td>
                                <td><button class="btn btn-primary" onclick="showDetails('Projet Alpha')">Consulter</button></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Projet Beta</td>
                                <td><button class="btn btn-primary" onclick="showDetails('Projet Beta')">Consulter</button></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Projet Gamma</td>
                                <td><button class="btn btn-primary" onclick="showDetails('Projet Gamma')">Consulter</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="graphs" role="tabpanel" aria-labelledby="graphs-tab">
                    <h5>Graphiques de Synthèse</h5>
                    <canvas id="myChart" width="400" height="200"></canvas>
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                        const ctx = document.getElementById('myChart').getContext('2d');
                        const myChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai'],
                                datasets: [{
                                    label: 'Données de Projet',
                                    data: [12, 19, 3, 5, 2],
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.2)',
                                        'rgba(54, 162, 235, 0.2)',
                                        'rgba(255, 206, 86, 0.2)',
                                        'rgba(75, 192, 192, 0.2)',
                                        'rgba(153, 102, 255, 0.2)'
                                    ],
                                    borderColor: [
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(153, 102, 255, 1)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    </script>
                </div>
            </div>
        </div>

    </div>





















    <!-- Bootstrap JS -->

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('voirProjets').addEventListener('click', function() {
            document.getElementById('tabsContainer').style.display = 'block';
        });

        // Recherche automatique
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#dataTable tbody tr');
            rows.forEach(row => {
                const cells = row.getElementsByTagName('td');
                const textContent = Array.from(cells).map(cell => cell.textContent.toLowerCase()).join(' ');
                row.style.display = textContent.includes(filter) ? '' : 'none';
            });
        });

        function showDetails(projectName) {
            alert(`Détails pour ${projectName}`);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script pour l'animation des chiffres et profil déplaçable -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fonction pour animer les chiffres
            function animateValue(id, start, end, duration) {
                let obj = document.getElementById(id);
                let range = end - start;
                let current = start;
                let increment = end > start ? 1 : -1;
                let stepTime = Math.abs(Math.floor(duration / range));

                let timer = setInterval(function() {
                    current += increment;
                    obj.innerText = current;
                    if (current === end) {
                        clearInterval(timer);
                    }
                }, stepTime);
            }

            // Animation des valeurs
            animateValue("totalProjects", 0, 35, 1500); // Exemple : 35 projets
            animateValue("totalPrograms", 0, 12, 1500); // Exemple : 12 programmes
            animateValue("totalFunds", 0, 150, 1500); // Exemple : 150 millions de fonds

            // Permet de rendre le profil flottant et déplaçable
            const userProfile = document.getElementById('userProfile');
            let isDragging = false;
            let offsetX = 0;
            let offsetY = 0;

            userProfile.addEventListener('mousedown', function(e) {
                isDragging = true;
                offsetX = e.clientX - userProfile.offsetLeft;
                offsetY = e.clientY - userProfile.offsetTop;
                userProfile.style.cursor = 'grabbing';
            });

            document.addEventListener('mousemove', function(e) {
                if (isDragging) {
                    userProfile.style.left = e.clientX - offsetX + 'px';
                    userProfile.style.top = e.clientY - offsetY + 'px';
                }
            });

            document.addEventListener('mouseup', function() {
                isDragging = false;
                userProfile.style.cursor = 'move';
            });
        });

        // Fonction pour afficher/masquer le menu déroulant
        function toggleDropdown(event) {
            const dropdownMenu = document.getElementById('dropdownMenu');

            // Vérifiez si le menu est déjà affiché
            if (dropdownMenu.style.display === 'none' || dropdownMenu.style.display === '') {
                dropdownMenu.style.display = 'block';
            } else {
                dropdownMenu.style.display = 'none';
            }

            // Empêcher la fermeture du menu lorsque l'on clique sur l'icône
            event.stopPropagation();
        }
        // Ferme le menu déroulant si l'utilisateur clique à l'extérieur
        document.addEventListener('click', function() {
            const dropdownMenu = document.getElementById('dropdownMenu');
            dropdownMenu.style.display = 'none';
        });


        // Fermer le menu déroulant en cliquant à l'extérieur
        window.onclick = function(event) {
            const dropdownMenu = document.getElementById('dropdownMenu');
            if (!event.target.matches('.fas')) {
                dropdownMenu.classList.remove('show');
            }
        }
    </script>
</body>
