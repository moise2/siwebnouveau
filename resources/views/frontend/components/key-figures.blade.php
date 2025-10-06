<div id="chiffres-cles" class="container">
    <div class="row">
        <div class="col-12 text-left mb-5">
            <h2 class="section-title">Chiffres Clés</h2>
            <p class="section-subtitle">Taux d'exécution physiques et financières</p>
        </div>
        
        <div class="position-relative">
            <button class="carousel-control prev" id="prevBtn">&lt;</button>
            <button class="carousel-control next" id="nextBtn">&gt;</button>
            
            <div class="key-figures-wrapper">
                <div id="key-figures-container" class="row flex-nowrap">
                    <!-- Data will be populated here dynamically -->
                </div>
            </div>

            <div class="carousel-pagination" id="carousel-bullets">
                <!-- Bullets will be added here dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="reformeDetailsModal" tabindex="-1" aria-labelledby="reformeDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reformeDetailsModalLabel">Détails de la réforme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="reformeDetailsContent">
                <!-- Le contenu sera injecté dynamiquement -->
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadReformsData = async () => {
        try {
            const loginResponse = await axios.post('/proxy/login');
            const token = loginResponse.data.access_token;

            const response = await axios.get('/proxy/reformes-stats', {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            if (response.data.success) {
                const reforms = response.data.data;
                const container = document.getElementById('key-figures-container');
                
                reforms.forEach(reform => {
                    const card = `
                        <div class="col-md-4">
                            <div class="key-figures-card" onclick="showReformeDetails(${JSON.stringify(reform).replace(/"/g, '&quot;')})">
                                <div class="exercise-badge">
                                    ${reform.exercice_libelle}
                                </div>
                                <div class="card-content">
                                    <div class="title-row">
                                        <h4>${reform.libelle}</h4>
                                    </div>
                                    <div class="progress-items">
                                        <div class="progress-item physical">
                                            <span class="label">Physiques</span>
                                            <div class="progress-bar">
                                                <span class="percentage">${parseFloat(reform.taux_execution_physique_moyen || 0).toFixed(2)}%</span>
                                            </div>
                                        </div>
                                        <div class="progress-item financial">
                                            <span class="label">Financiers</span>
                                            <div class="progress-bar">
                                                <span class="percentage">${parseFloat(reform.taux_execution_financiere_moyen || 0).toFixed(2)}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.innerHTML += card;
                });

                // Initialisation du carrousel
                initCarousel();
            }
        } catch (error) {
            console.error('Erreur lors du chargement des données:', error);
        }
    };

    loadReformsData();
});

// Fonction pour gérer le carrousel
function initCarousel() {
    const container = document.getElementById('key-figures-container');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const bulletsContainer = document.getElementById('carousel-bullets');
    
    const cardWidth = container.querySelector('.col-md-4').offsetWidth;
    const cardsPerSlide = 3;
    let currentSlide = 0;
    
    // Calculer le nombre total de slides
    const totalSlides = Math.ceil(container.children.length / cardsPerSlide);
    
    // Créer les bullets de pagination
    for (let i = 0; i < totalSlides; i++) {
        const bullet = document.createElement('div');
        bullet.className = `bullet ${i === 0 ? 'active' : ''}`;
        bullet.addEventListener('click', () => goToSlide(i));
        bulletsContainer.appendChild(bullet);
    }

    function updateBullets() {
        const bullets = bulletsContainer.getElementsByClassName('bullet');
        Array.from(bullets).forEach((bullet, index) => {
            bullet.classList.toggle('active', index === currentSlide);
        });
    }

    function goToSlide(slideIndex) {
        currentSlide = slideIndex;
        const scrollPosition = slideIndex * (cardWidth * cardsPerSlide);
        container.style.transform = `translateX(-${scrollPosition}px)`;
        updateBullets();
    }

    prevBtn.addEventListener('click', () => {
        if (currentSlide > 0) {
            goToSlide(currentSlide - 1);
        }
    });

    nextBtn.addEventListener('click', () => {
        if (currentSlide < totalSlides - 1) {
            goToSlide(currentSlide + 1);
        }
    });
}

// Fonction pour afficher les détails dans le modal
function showReformeDetails(reform) {
    const modalContent = `
        <div class="reform-details">
            <h4>${reform.libelle}</h4>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nombre de programmes :</strong> ${reform.nombre_programmes}</p>
                    <p><strong>Nombre d'actions :</strong> ${reform.nombre_actions}</p>
                    <p><strong>Nombre d'activités :</strong> ${reform.nombre_activites}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Taux d'exécution physique :</strong> ${parseFloat(reform.taux_execution_physique_moyen || 0).toFixed(2)}%</p>
                    <p><strong>Taux d'exécution financier :</strong> ${parseFloat(reform.taux_execution_financiere_moyen || 0).toFixed(2)}%</p>
                </div>
            </div>
            ${reform.programmes ? `
                <hr>
                <h5>Programmes associés :</h5>
                <ul>
                    ${reform.programmes.map(prog => `
                        <li>
                            <strong>${prog.intitule}</strong>
                            <br>Code: ${prog.code}
                        </li>
                    `).join('')}
                </ul>
            ` : ''}
        </div>
    `;

    document.getElementById('reformeDetailsContent').innerHTML = modalContent;
    const modal = new bootstrap.Modal(document.getElementById('reformeDetailsModal'));
    modal.show();
}
</script>

<style>
.key-figures-wrapper {
    overflow-x: hidden;
    position: relative;
    padding: 0 40px;
    margin-bottom: 30px;
}

.key-figures-container {
    display: flex;
    transition: transform 0.5s ease;
    gap: 24px;
}

.key-figures-card {
    position: relative;
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    margin-bottom: 20px;
    border: 1px solid #f0f0f0;
    height: 100%;
}

.exercise-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #dc3545;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
}

.progress-items {
    margin-top: 20px;
}

.progress-item {
    margin-bottom: 15px;
    padding: 10px;
    border-radius: 8px;
}

.progress-item.physical {
    background-color: #e8f4ff;
}

.progress-item.financial {
    background-color: #ffe8e8;
}

.progress-item .label {
    display: block;
    margin-bottom: 5px;
    color: #666;
}

.progress-bar {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    height: 30px;
}

.percentage {
    font-weight: bold;
    color: #333;
}

.title-row h4 {
    font-size: 1rem;
    line-height: 1.4;
    margin-bottom: 20px;
    margin-top: 30px;
}

.key-figures-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.carousel-control {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    border: none;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    cursor: pointer;
    z-index: 10;
    transition: all 0.3s ease;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.carousel-control:hover {
    background: #c82333;
    transform: translateY(-50%) scale(1.1);
}

.prev {
    left: -5px;
}

.next {
    right: -5px;
}

.carousel-pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 20px;
}

.bullet {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #ddd;
    cursor: pointer;
    transition: all 0.3s ease;
}

.bullet.active {
    background: #dc3545;
    transform: scale(1.2);
}

/* Style pour le modal */
.reform-details ul {
    padding-left: 20px;
}

.reform-details li {
    margin-bottom: 10px;
}
</style>


