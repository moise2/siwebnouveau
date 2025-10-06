<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- ... autres métadonnées ... -->
    @yield('styles')
    @include('frontend.partials.head')
   
</head>
<body>
@include('frontend.partials.header')

<main>
    @yield('content')
</main>



<script>
    document.addEventListener("DOMContentLoaded", function () {
        @if (session('success'))
            toastr.success('{{ session('success') }}', 'Succès', {
                closeButton: true,
                progressBar: true,
                timeOut: 8000
            });
        @endif

        @if (session('error'))
            toastr.error('{{ session('error') }}', 'Erreur', {
                closeButton: true,
                progressBar: true,
                timeOut: 8000
            });
        @endif
    });
</script>






@include('frontend.partials.footer')
<!-- Bouton "Retour en haut" -->
<a href="#" id="back-to-top" class="back-to-top">
    <i class="fas fa-chevron-up"></i>
</a>

<script>
    // Afficher le bouton lorsque l'utilisateur fait défiler vers le bas
window.addEventListener('scroll', function() {
    const backToTop = document.getElementById('back-to-top');
    if (window.scrollY > 300) {
        backToTop.classList.add('show');
    } else {
        backToTop.classList.remove('show');
    }
});

// Animation pour revenir en haut de la page
document.getElementById('back-to-top').addEventListener('click', function(e) {
    e.preventDefault();
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

</script>


<script src="assets/bootstrap/js/bootstrap.min.js"></script>

<script src="assets/js/Simple-Slider.js"></script>



@yield('scripts')
</body>
</html>
