<div class="container-fluid">
    <div class="row">
        <div class="col-12 p-0">
            <div class="key-figures-section">
                <h2 class="figures-title">Chiffres Clés</h2>
                <div class="row figures-charts">
                    <!-- Première ligne -->
                    <div class="figure-card">
                        <h3 class="chart-title">Population</h3>
                        <i class="fas fa-users chart-icon"></i>
                        <div class="chart-legend-side">
                            <canvas id="populationChart" class="circle-chart"></canvas>
                            <div class="chart-legend"></div>
                        </div>
                    </div>
                    <div class="figure-card">
                        <h3 class="chart-title">Principaux Secteurs</h3>
                        <i class="fas fa-industry chart-icon"></i>
                        <div class="chart-legend-side">
                            <canvas id="secteursChart" class="circle-chart"></canvas>
                            <div class="chart-legend"></div>
                        </div>
                    </div>
                    <div class="figure-card">
                        <h3 class="chart-title">Entreprises</h3>
                        <i class="fas fa-chart-pie chart-icon"></i>
                        <p class="chart-amount">5.1 Milliards USD</p>
                        <p class="chart-info">Chiffre d'affaires des entreprises togolaises en 2018</p>
                    </div>
                    <!-- Deuxième ligne -->
                    <div class="figure-card">
                        <h3 class="chart-title">Agriculture</h3>
                        <i class="fas fa-seedling chart-icon" style="color: #38761d;"></i>
                        <p class="chart-amount">Premier exportateur de produits agricoles bio</p>
                        <p class="chart-info">de la CEDEAO vers l'Europe</p>
                    </div>
                    <div class="figure-card">
                        <h3 class="chart-title">Finances</h3>
                        <i class="fas fa-university chart-icon" style="color: #cc0000;"></i>
                        <p class="chart-amount">Première place bancaire en Afrique de l'ouest</p>
                        <p class="chart-info">par nombre de représentations d'institutions financières</p>
                    </div>
                    <div class="figure-card">
                        <h3 class="chart-title">Business</h3>
                        <i class="fas fa-briefcase chart-icon" style="color: #ffd700;"></i>
                        <p class="chart-amount">85% de croissance IDE 2020 par rapport à 2019</p>
                        <p class="chart-info">Investissement Direct Etranger</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.key-figures-section . {
    width: 100%;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    margin: 0;
    background-color: #f9f7e1;
}

.figures-title {
    font-size: 2rem;
    font-weight: 700;
    color: #2a2a2a;
    margin-bottom: 20px;
    font-family: 'Poppins', sans-serif;
}

.figures-charts {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    padding: 0 20px;
}

.figure-card {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    padding: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 240px;
}

.chart-icon {
    font-size: 2rem;
    color: #38761d;
    margin-bottom: 10px;
}

.chart-title {
    font-size: 1.3rem;
    color: #2a2a2a;
    font-weight: 600;
    margin-bottom: 10px;
}

.chart-amount {
    font-size: 1rem;
    font-weight: 700;
    color: #333333;
    margin: 8px 0;
    text-transform: capitalize;
}

.chart-info {
    font-size: 0.9rem;
    color: #555555;
    text-align: center;
    margin-bottom: 5px;
    text-transform: capitalize;
}

.circle-chart {
    width: 150px !important;
    height: 150px !important;
    margin-right: 15px;
}

.chart-legend-side {
    display: flex;
    align-items: center;
}

.chart-legend {
    font-size: 0.9rem;
    color: #555555;
}

@media (max-width: 768px) {
    .figures-charts {
        grid-template-columns: 1fr;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Diagramme en cercle pour la Population
    const ctxPopulation = document.getElementById('populationChart').getContext('2d');
    new Chart(ctxPopulation, {
        type: 'pie',
        data: {
            labels: ['Femmes', 'Hommes', 'Jeunes'],
            datasets: [{
                data: [40, 40, 20],
                backgroundColor: ['#ffd700', '#38761d', '#cc0000']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'right', // Positionner à droite
                    labels: {
                        font: {
                            size: 10,
                            family: 'Poppins'
                        }
                    }
                }
            },
            layout: {
                padding: {
                    right: 50 // Ajuste l'espace à droite pour les légendes
                }
            }
        }
    });

    // Diagramme en cercle pour les Principaux Secteurs
    const ctxSecteurs = document.getElementById('secteursChart').getContext('2d');
    new Chart(ctxSecteurs, {
        type: 'pie',
        data: {
            labels: ['Autres', 'Agriculture et Mines'],
            datasets: [{
                data: [50, 50],
                backgroundColor: ['#38761d', '#cc0000']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'right', // Positionner à droite
                    labels: {
                        font: {
                            size: 10,
                            family: 'Poppins'
                        }
                    }
                }
            },
            layout: {
                padding: {
                    right: 50 // Ajuste l'espace à droite pour les légendes
                }
            }
        }
    });
});
</script>


