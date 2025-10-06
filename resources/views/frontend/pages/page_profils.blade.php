<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Suivi des Réformes</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js CDN -->

</head>

<body>
    <!-- Profil utilisateur flottant et déplaçable -->
    <div class="user-profile" id="userProfile">
        <img src="{{ asset($user->profile_photo_path ?? 'https://via.placeholder.com/50') }}" alt="Photo de profil">
        <div class="user-info">
            <h4>{{ $user->name }}</h4>
            <p>{{ $user->role }}</p> <!-- Assurez-vous d'avoir un champ 'role' pour l'utilisateur -->
        </div>
        <div class="user-actions">
            <i class="fas fa-cog" title="Paramètres" onclick="toggleDropdown(event)"></i>
            <i class="fas fa-user" title="Options" onclick="toggleDropdown(event)"></i>
            <div class="dropdown-menu" id="dropdownMenu" style="display: none;">
                <a href="#">Paramètres</a>
                <a href="{{ route('logout') }}">Déconnexion</a>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="container">
        <!-- Titre du tableau de bord -->
        <div class="dashboard-header">
            <h1>Tableau de Bord - Suivi des Réformes</h1>
        </div>

        <!-- Section des chiffres clés -->
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <h2 class="counter" id="totalProjects">{{ $totalProjects }}</h2>
                    <p>Total de Projets</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <h2 class="counter" id="totalPrograms">{{ $totalPrograms }}</h2>
                    <p>Total de Programmes</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <h2 class="counter" id="totalFunds">{{ number_format($totalFunds, 2, ',', ' ') }} M</h2>
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
            <br><br>
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
                            @foreach($projects as $project)
                                <tr>
                                    <td>{{ $project->id }}</td>
                                    <td>{{ $project->name }}</td>
                                    <td><button class="btn btn-primary" onclick="showDetails('{{ $project->name }}')">Consulter</button></td>
                                </tr>
                            @endforeach
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
                                    data: [12, 19, 3, 5, 2],  // Vous pouvez remplacer par des données dynamiques
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

    <!-- Scripts similaires aux précédents -->
</body>

</html>
