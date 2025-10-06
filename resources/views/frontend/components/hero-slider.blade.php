
<div class="carousel-inner">
        <!-- Articles -->
        @foreach($slides['articles'] as $index => $article)
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <div class="carousel-img-wrapper">
                <img  src="{{ Str::startsWith($article->image, 'http') ? $article->image : asset('storage/' . $article->image) }}" alt="{{ htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8') }}" 
                class="d-block w-100">

                    <!-- <img src="{{ asset('storage/' . $article->image) }}" alt="{{ htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8') }}" class="d-block w-100"> -->
                    <div class="carousel-gradient-overlay"></div>
                </div>
                <div class="carousel-caption">
                    <a href="{{ route('articles.show', ['slug' => $article->slug]) }}">
                        <h2>{{ htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8') }}</h2>
                    </a>
                </div>
            </div>
        @endforeach

        <!-- Documents -->
        @foreach($slides['documents'] as $index => $document)
            <div class="carousel-item {{ $index === 0 && !$slides['articles'] ? 'active' : '' }}">
                <div class="carousel-img-wrapper">
                    <img src="{{ asset('storage/still-life-documents-stack.jpg') }}" 
                        alt="{{ htmlspecialchars($document->title, ENT_QUOTES, 'UTF-8') }}" 
                        class="d-block w-100">
                    <div class="carousel-gradient-overlay"></div>
                </div>
                <div class="carousel-caption">
                    <i class="fas fa-file-alt text-light"></i>

                    <a href="{{ url($document->download_link) }}" target="_blank">
                        <h2 class="text-light fw-bold">{{ htmlspecialchars($document->title, ENT_QUOTES, 'UTF-8') }}</h2>
                    </a>
                </div>
            </div>
        @endforeach




        <!-- Events -->
        @foreach($slides['events'] as $index => $event)
            <div class="carousel-item {{ $index === 0 && !$slides['articles'] && !$slides['documents'] ? 'active' : '' }}">
                <div class="carousel-img-wrapper">
                    <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ htmlspecialchars($event->title, ENT_QUOTES, 'UTF-8') }}" class="d-block w-100">
                    <div class="carousel-gradient-overlay"></div>
                </div>
                <div class="carousel-caption">
                    <i class="fas fa-calendar-alt text-light"></i>
                    <a href="{{ route('events.show', ['slug' => $event->slug]) }}">
                        <h2 class="text-light fw-bold">{{ htmlspecialchars($event->title, ENT_QUOTES, 'UTF-8') }}</h2>
                    </a>
                    <div class="slide-underline bg-danger"></div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Navigation controls -->
    {{-- <button data-bs-target="#elegantCarousel" data-bs-slide="prev" class="carousel-control-prev"></button>
    <button data-bs-target="#elegantCarousel" data-bs-slide="next" class="carousel-control-next"></button> --}}


<!-- CSS -->
<style>
 .carousel-item {
    transition: transform 0.5s ease-in-out;
}

.carousel-img-wrapper {
    position: relative;
    height: 500px;
}

.carousel-img-wrapper img {
    object-fit: cover;
    height: 100%;
    filter: brightness(70%);
}

.carousel-gradient-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to top, rgba(2, 86, 142, 0.631), rgba(1, 107, 152, 0.1));
}

.carousel-caption {
    bottom: 20%;
    text-align: left;
    padding: 10px;
    border-radius: 5px;
    shadow: 0 4px 15px rgba(0, 0, 0, 0.5); /* Ombre au lieu du background */
}

.carousel-caption h2 {
    margin: 0;
    line-height: 1.2;
    font-family: 'Poppins', sans-serif;
    color: white;
    position: relative;
    text-decoration: none; /* Assure qu'il n'y a pas de soulignement */
    outline: none; /* Supprime le contour bleu */

}

/* Soulignement rouge légèrement décalé */
.carousel-caption h2::before {
    content: '';
    position: absolute;
    bottom: -10px; /* Décalage du soulignement par rapport au titre */
    left: 0;
    height: 3px;
    width: 80px;
    background-color: red;
    border-radius: 10px;
    transition: width 0.3s ease-in-out;
}

/* Suppression du soulignement au survol */
/* Suppression du trait bleu au survol */
.carousel-caption h2:hover {
    text-decoration: none;
    outline: none;
}
.carousel-item:hover .carousel-caption h2::before {
    width: 420px; /* Le soulignement s'allonge au survol */
}

/* Responsiveness for title font size */
@media (min-width: 576px) {
    .carousel-caption h2 {
        font-size: 0.5rem;
    }
}

@media (min-width: 768px) {
    .carousel-caption h2 {
        font-size: 2rem;
    }
}

@media (min-width: 992px) {
    .carousel-caption h2 {
        font-size: 1.5rem;
    }
}

@media (min-width: 1200px) {
    .carousel-caption h2 {
        font-size: 2rem;
    }
}

.slide-underline {
    height: 3px;
    width: 100px;
    margin-top: 10px;
    background-color: red;
    border-radius: 10px; /* Rounded corners for the underline */
    transition: width 0.3s ease-in-out;
}

.carousel-item:hover .slide-underline {
    width: 150px;
}

/* Fix the z-index issue with megamenus */
.carousel {
    z-index: 1;
}

.megamenu {
    z-index: 1000;
}

/* Assurez-vous que les liens autour des titres n'ont pas de soulignement ni de contour */
.carousel-caption a {
    text-decoration: none; /* Supprime le soulignement sur les liens */
    outline: none; /* Supprime le contour bleu */
}

/* Empêche le soulignement bleu au survol */
.carousel-caption a:hover {
    text-decoration: none; /* Empêche le soulignement au survol */
    outline: none; /* Empêche le contour bleu au survol */
}

/* Règle le h2 pour le soulignement rouge personnalisé */
.carousel-caption h2 {
    margin: 0;
    line-height: 1.2;
    font-family: 'Poppins', sans-serif;
    color: white;
    position: relative;
    text-decoration: none; /* Assure qu'il n'y a pas de soulignement */
    outline: none; /* Supprime le contour bleu */
}

/* Soulignement rouge légèrement décalé */
.carousel-caption h2::before {
    content: '';
    position: absolute;
    bottom: -10px; /* Décalage du soulignement par rapport au titre */
    left: 0;
    height: 3px;
    width: 80px;
    background-color: red;
    border-radius: 10px;
    transition: width 0.3s ease-in-out;
}

/* Au survol, le soulignement rouge s'allonge */
.carousel-item:hover .carousel-caption h2::before {
    width: 420px;
}
.carousel {
    z-index: 1;
}

.megamenu {
    z-index: 1000;
}



/* Applique l'effet de fade-in */
.carousel-item.fade-in {
    opacity: 1;
    transition: opacity 0.5s ease-in-out;
}

/* Masque les autres slides */
.carousel-item {
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
}

/* Les slides actives sont pleinement visibles */
.carousel-item.active {
    opacity: 1;
    transition: opacity 0.5s ease-in-out;
}

</style>


<script>
    document.addEventListener('DOMContentLoaded', function() {
    const carouselItems = document.querySelectorAll('.carousel-item');
    const transitionDuration = 500; // Durée de l'animation en millisecondes

    // Applique une animation de fade-in/fade-out lors du changement de slide
    function setCarouselFadeEffect() {
        // Boucle sur chaque élément du carrousel
        carouselItems.forEach((item) => {
            // Applique l'animation de transition uniquement sur les slides actives
            item.addEventListener('transitionend', () => {
                // Supprime la classe fade si la transition est terminée
                item.classList.remove('fade-in');
            });

            // Lorsque l'élément devient actif
            if (item.classList.contains('active')) {
                // Ajoute la classe fade-in pour animer l'apparition
                item.classList.add('fade-in');
            }
        });
    }

    // Fonction pour activer la première slide avec animation fade
    function activateFirstSlide() {
        const activeSlide = document.querySelector('.carousel-item.active');
        if (activeSlide) {
            activeSlide.classList.add('fade-in');
        }
    }

    // Applique l'effet à l'initialisation
    setCarouselFadeEffect();
    activateFirstSlide();
});






document.addEventListener('DOMContentLoaded', function() {
    const carouselItems = document.querySelectorAll('.carousel-item');
    const transitionDuration = 500; // Durée de l'animation en millisecondes
    const carouselInterval = 5000; // Temps en millisecondes entre chaque slide (5 secondes)
    let currentIndex = 0;

    // Fonction pour changer de slide
    function showNextSlide() {
        // Enlève la classe active et fade-in de la slide actuelle
        carouselItems[currentIndex].classList.remove('active', 'fade-in');

        // Incrémente l'index et revient au début si on atteint la fin des slides
        currentIndex = (currentIndex + 1) % carouselItems.length;

        // Active la prochaine slide avec l'animation fade-in
        carouselItems[currentIndex].classList.add('active', 'fade-in');
    }

    // Fonction pour activer la première slide avec animation fade
    function activateFirstSlide() {
        const activeSlide = document.querySelector('.carousel-item.active');
        if (activeSlide) {
            activeSlide.classList.add('fade-in');
        }
    }

    // Lancement du carrousel automatique
    setInterval(showNextSlide, carouselInterval);

    // Applique l'effet fade à l'initialisation
    activateFirstSlide();
});


</script>
