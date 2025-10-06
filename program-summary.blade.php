<div id="key-figures" class="container-fluid custom-container" style="border-top: 2px solid #e63946; border-radius: 12px; background-color: #f8f9fa; padding: 20px;">
    <div class="row justify-content-center">
        <!-- Première ligne : Population, Secteurs et Entreprises -->
        <div class="col-lg-4 col-md-6 d-flex">
            <div class="figure-card">
                <i class="fas fa-users chart-icon"></i>
                <h3 class="chart-title">Population</h3>
                <p class="chart-info">
                    <span>Hommes : 4,3 millions</span>
                    <span>Femmes : 4,4 millions</span>
                </p>
                <!-- <p class="chart-info">Vers l'Europe</p> -->
                <p class="chart-amount">Jeunes (moins de 25 ans) : 45%</p>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 d-flex">
            <div class="figure-card">
                <i class="fas fa-industry chart-icon"></i>
                <h3 class="chart-title">Principaux Secteurs</h3>
                <p class="chart-info">
                    <span>Agriculture : 40%</span>
                    <span>Industrie : 28%</span>
                    <span>Services : 32%</span>
                </p>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 d-flex">
            <div class="figure-card">
                <i class="fas fa-chart-pie chart-icon"></i>
                <h3 class="chart-title">Entreprises</h3>
                <p class="chart-amount">$5.1 Mds USD</p>
                <p class="chart-info">Chiffre d'affaires (2018)</p>
            </div>
        </div>
    </div>
    <!-- Deuxième ligne : Secteurs Spécifiques -->
    <div class="row">
        <div class="col-lg-4 col-md-6 d-flex">
            <div class="figure-card">
                <i class="fas fa-seedling chart-icon"></i>
                <h3 class="chart-title">Agriculture</h3>
                <p class="chart-amount">Exportateur bio #1 CEDEAO</p>
                <p class="chart-info">Vers l'Europe</p>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 d-flex">
            <div class="figure-card">
                <i class="fas fa-university chart-icon"></i>
                <h3 class="chart-title">Finances</h3>
                <p class="chart-amount">Top 1 Ouest Africain</p>
                <p class="chart-info">Représentations bancaires</p>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 d-flex">
            <div class="figure-card">
                <i class="fas fa-briefcase chart-icon"></i>
                <h3 class="chart-title">Business</h3>
                <p class="chart-amount">+85% IDE (2020)</p>
                <p class="chart-info">Croissance annuelle</p>
            </div>
        </div>
    </div>
</div>
<style>
    /* Global Styles */
#key-figures {
    font-family: 'Poppins', sans-serif;
    background-color: #f4f4f4;
}

/* Section */
#key-figures .key-figures-section {
    padding: 20px;
    text-align: center;
    background-color: #ffffff;
    border-radius: 12px;
}

/* Figures Grid */
#key-figures .row {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: stretch;
}

#key-figures .col-lg-4, #key-figures .col-md-6 {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

#key-figures .figure-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 15px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
    width: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 160px;
    max-height: 200px;
}

#key-figures .figure-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

#key-figures .chart-icon {
    font-size: 2.3rem;
    color: #e63946;
    margin-bottom: 10px;
}

#key-figures .chart-title {
    font-size: 1.2rem;
    font-weight: bold;
    color: #343a40;
    margin-bottom: 5px;
}

#key-figures .chart-info, #key-figures .chart-amount {
    font-size: 0.9rem;
    color: #6c757d;
    margin-top: 8px;
}

#key-figures .chart-amount {
    font-weight: 600;
    color: #e63946;
}

/* Aligner les secteurs horizontalement */
#key-figures .chart-info {
    display: flex;
    justify-content: center;
    padding: 0;
    margin: 0;
}

#key-figures .chart-info span {
    font-size: 0.9rem;
    color: #6c757d;
    margin-right: 15px; /* Ajuste l'espacement entre les secteurs */
}

#key-figures .chart-info span:last-child {
    margin-right: 0; /* Ne pas ajouter d'espacement après le dernier élément */
}

/* Centrages spécifiques pour les paragraphes */
#key-figures .chart-info {
    text-align: center;
}

#key-figures .chart-info p {
    text-align: center;
}

/* Réduire l'épaisseur de la bande rouge en haut pour un seul élément */
#key-figures .custom-container {
    border-top: 1px solid #e63946; /* Bande rouge plus fine et spécifique */
}

/* Responsiveness */
@media (max-width: 768px) {
    #key-figures .row {
        flex-direction: column;
        align-items: center;
    }

    #key-figures .figure-card {
        margin-bottom: 20px;
        width: 90%;
    }
}

</style>