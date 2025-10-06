<div id="dynamic-key-figures" class="container-fluid py-5">

    <div class="row gy-4">
        <!-- Card 1 -->
        <div class="col-lg-4 col-md-6">
            <div class="dynamic-card">
                <div class="card-content">
                    <div class="icon-container">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="card-title">Population</h3>
                    <p class="card-text"><strong>Hommes :</strong> 4,3 millions</p>
                    <p class="card-text"><strong>Femmes :</strong> 4,4 millions</p>
                    <p class="card-highlight">Jeunes : 45%</p>
                </div>
                <div class="card-background" style="background-image: url('https://via.placeholder.com/400x300');"></div>
            </div>
        </div>
        <!-- Card 2 -->
        <div class="col-lg-4 col-md-6">
            <div class="dynamic-card">
                <div class="card-content">
                    <div class="icon-container">
                        <i class="fas fa-industry"></i>
                    </div>
                    <h3 class="card-title">Secteurs Clés</h3>
                    <p class="card-text">Agriculture : 40%</p>
                    <p class="card-text">Industrie : 28%</p>
                    <p class="card-text">Services : 32%</p>
                </div>
                <div class="card-background" style="background-image: url('https://via.placeholder.com/400x300/abcdef');"></div>
            </div>
        </div>
        <!-- Card 3 -->
        <div class="col-lg-4 col-md-6">
            <div class="dynamic-card">
                <div class="card-content">
                    <div class="icon-container">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="card-title">Croissance IDE</h3>
                    <p class="card-text">+85% (2020)</p>
                    <p class="card-highlight">Top en Afrique de l'Ouest</p>
                </div>
                <div class="card-background" style="background-image: url('https://via.placeholder.com/400x300/ff6347');"></div>
            </div>
        </div>
        <!-- Card 4 -->
        <div class="col-lg-4 col-md-6">
            <div class="dynamic-card">
                <div class="card-content">
                    <div class="icon-container">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3 class="card-title">Environnement</h3>
                    <p class="card-text">Forêts protégées : 12%</p>
                    <p class="card-text">Zones urbaines : 20%</p>
                    <p class="card-highlight">Objectif : +5% en 2025</p>
                </div>
                <div class="card-background" style="background-image: url('https://via.placeholder.com/400x300/2ecc71');"></div>
            </div>
        </div>
        <!-- Card 5 -->
        <div class="col-lg-4 col-md-6">
            <div class="dynamic-card">
                <div class="card-content">
                    <div class="icon-container">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3 class="card-title">Éducation</h3>
                    <p class="card-text">Taux de scolarisation : 90%</p>
                    <p class="card-text">Taux d'alphabétisation : 75%</p>
                    <p class="card-highlight">Progression annuelle : +3%</p>
                </div>
                <div class="card-background" style="background-image: url('https://via.placeholder.com/400x300/3498db');"></div>
            </div>
        </div>
        <!-- Card 6 -->
        <div class="col-lg-4 col-md-6">
            <div class="dynamic-card">
                <div class="card-content">
                    <div class="icon-container">
                        <i class="fas fa-hospital"></i>
                    </div>
                    <h3 class="card-title">Santé</h3>
                    <p class="card-text">Espérance de vie : 72 ans</p>
                    <p class="card-text">Vaccination : 95%</p>
                    <p class="card-highlight">Objectif : 98% d'ici 2025</p>
                </div>
                <div class="card-background" style="background-image: url('https://via.placeholder.com/400x300/e74c3c');"></div>
            </div>
        </div>
    </div>
</div>


<style>
/* Global Styling for #dynamic-key-figures */
#dynamic-key-figures {
    font-family: 'Poppins', sans-serif;
    background: #f5f7fa;
    padding: 60px 20px;
}

#dynamic-key-figures .animated-title {
    font-size: 2.5rem;
    color: #e63946;
    /* text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3); */
    animation: fadeInDown 1s ease-in-out;
}

#dynamic-key-figures .animated-subtitle {
    font-size: 1.2rem;
    color: #495057;
    animation: fadeInUp 1s ease-in-out;
}

/* Dynamic Card Styling for #dynamic-key-figures */
#dynamic-key-figures .dynamic-card {
    position: relative;
    overflow: hidden;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    transition: transform 0.5s, box-shadow 0.5s;
    height: 300px;
    cursor: pointer;
}

#dynamic-key-figures .dynamic-card:hover {
    transform: translateY(-10px) scale(1.05);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
}

#dynamic-key-figures .dynamic-card .card-content {
    position: relative;
    z-index: 2;
    padding: 20px;
    background: rgba(255, 255, 255, 0.85);
    border-radius: 15px;
    transition: background 0.3s;
}

#dynamic-key-figures .dynamic-card:hover .card-content {
    background: rgba(255, 255, 255, 1);
}

#dynamic-key-figures .dynamic-card .icon-container {
    width: 70px;
    height: 70px;
    background: #e63946;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 15px;
    color: white;
    font-size: 2rem;
}

#dynamic-key-figures .dynamic-card .card-title {
    font-size: 1.5rem;
    color: #343a40;
    margin-bottom: 10px;
}

#dynamic-key-figures .dynamic-card .card-text {
    font-size: 1rem;
    color: #6c757d;
    margin: 5px 0;
}

#dynamic-key-figures .dynamic-card .card-highlight {
    font-size: 1.1rem;
    font-weight: 600;
    color: #e63946;
}

#dynamic-key-figures .dynamic-card .card-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    opacity: 0.5;
    z-index: 1;
    transition: opacity 0.3s;
}

#dynamic-key-figures .dynamic-card:hover .card-background {
    opacity: 0.3;
}

/* Animations */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design for #dynamic-key-figures */
@media (max-width: 768px) {
    #dynamic-key-figures .dynamic-card {
        height: 250px;
    }

    #dynamic-key-figures .dynamic-card .icon-container {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
    }

    #dynamic-key-figures .dynamic-card .card-title {
        font-size: 1.2rem;
    }

    #dynamic-key-figures .dynamic-card .card-text {
        font-size: 0.9rem;
    }
}

</style>
