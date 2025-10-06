@extends('frontend.layouts.app')

@section('content')

<style>
    body {
        background-color: #f0f2f5; /* Light grey background for the entire page */
    }
    .background-image {
        /* Dégradé bleu vers vert qui couvrira le drapeau du bas vers le haut */
        /* La première couleur est le dégradé, la seconde est l'image de fond (le drapeau) */
        background: linear-gradient(to top, rgba(2, 158, 140, 0.5), rgba(2, 69, 100, 0.6)), /* Opacités ajustées pour mieux voir le drapeau */
                    url("{{ asset('assets/img/drapeautogolais.jpg') }}") center/cover no-repeat;
        
        background-size: cover;
        background-position: center;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff; /* Texte en blanc pour un bon contraste avec le dégradé */
        overflow: hidden; /* Cache tout ce qui dépasse */
    }

    /* L'ancien pseudo-élément ::before pour le drapeau n'est plus nécessaire car le drapeau est maintenant dans le background-image principal */
    /* .background-image::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url("{{ asset('assets/img/drapeautogolais.jpg') }}");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        opacity: 0.15;
        z-index: 1;
    } */

    .background-image .content-wrapper {
        position: relative;
        z-index: 2; /* S'assure que le contenu est au-dessus du dégradé et du drapeau */
        padding: 3rem;
        text-align: center; /* Assurez-vous que le texte est centré dans son wrapper */
    }

    .logo {
        max-width: 180px; /* Slightly larger logo */
        height: auto;
        z-index:1;
    }
    h2 {
        color: #ffffff; /* Assuré que le titre est blanc */
        font-weight: 400;
        font-size: 1.1rem; /* Diminution de la taille du titre h2 */
        margin-top: 20px;
    }
    .small-text {
        font-size: 1rem; /* Slightly larger text for better readability */
        color: #ffffff; /* Assuré que le texte est blanc */
        line-height: 1.6;
        padding: 0 20px; /* Add horizontal padding for text */
    }
    /* Styles personnalisés pour le message de session */
    .session-message-container {
        position: fixed; /* Changed to fixed to stay on top regardless of scroll */
        top: 20px; /* Ajustez selon le besoin */
        left: 50%;
        transform: translateX(-50%);
        z-index: 1050; /* Assure que le message est au-dessus de tout le reste */
        width: 90%; /* Prend une bonne largeur sur mobile */
        max-width: 500px; /* Limite la largeur sur desktop */
        text-align: center;
    }
    .session-message-container .alert {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25); /* Stronger shadow */
        border-radius: 0.75rem; /* More rounded corners */
        padding: 1.25rem 1.75rem; /* More padding */
        margin-bottom: 0; /* Pas de marge en bas si c'est le seul contenu du conteneur */
        font-size: 1.1rem; /* Larger font size */
    }

    /* Styles pour rendre le formulaire élégant */
    .login-form-card {
        background-color: #ffffff; /* White background for the form card */
        padding: 40px; /* More padding inside the form card */
        border-radius: 1.5rem; /* More rounded corners for the card */
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); /* Deeper shadow for a floating effect */
        transition: all 0.3s ease-in-out;
    }
    .login-form-card:hover {
        transform: translateY(-5px); /* Slight lift on hover */
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
    }
    .login-form-card h4 {
        font-weight: 600;
        color: #333;
        margin-bottom: 30px;
    }
    .form-control {
        border-radius: 0.5rem; /* Rounded input fields */
        padding: 0.75rem 1rem;
        font-size: 1rem;
        border: 1px solid #ced4da;
    }
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        border-radius: 0.75rem; /* Rounded button */
        padding: 0.8rem 1.5rem;
        font-size: 1.1rem;
        font-weight: 600;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }
    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
        transform: translateY(-2px);
    }
    a {
        color: #007bff;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }

    /* Media queries for responsiveness */
    @media (max-width: 991.98px) {
        .background-image {
            height: 300px; /* Fixed height on smaller screens */
            padding: 20px;
            text-align: center;
        }
        .background-image .text-center {
            max-width: 80%; /* Constrain text width on mobile */
        }
        .login-form-card {
            margin-top: 30px; /* Add space from the image part */
            margin-bottom: 30px;
            width: 100% !important; /* Make form wider on smaller screens */
        }
        #login-page .min-vh-100 {
            min-height: auto; /* Remove min-height to prevent overflow on small screens */
        }
    }
</style>
<div id="login-page" class="container-fluid mt-2 pt-1">
    
    <!-- Conteneur pour le message de session, positionné en haut de la page -->
    <div class="session-message-container">
        @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <div class="row min-vh-100">
        <!-- Colonne gauche avec image et texte -->
        <div class="col-lg-5 d-flex align-items-center justify-content-center background-image mt-5">
            <div class="content-wrapper">
                <img src="{{ asset('assets/img/20210406125513!Armoiries_du_Togo (1).png') }}" alt="Armoiries du Togo" class="img-fluid mb-3 logo">
                <h3 class="mb-3">Plateforme de Suivi des Réformes au Togo</h3>
                <p class="small-text">Propriété du Ministère de l'Economie et des Finances / Secrétariat Permanent pour le Suivi des Politiques de Réformes et des Programmes Financiers</p>
            </div>
        </div>

        <!-- Colonne droite -->
        <div class="col-lg-7 d-flex align-items-center justify-content-center">
            <div class="w-75 login-form-card">
                <h4 class="mb-4">Connexion</h4>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Se souvenir de moi</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>

                    <div class="mt-3 text-center">
                        <p>Vous n'avez pas de compte ? <a href="{{ route('register') }}">Inscrivez-vous</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script reCAPTCHA -->
<script src="https://www.google.com/recaptcha/api.js?render=6LdzBa8qAAAAAOgI_b-3adrulv5ak-NfVukmYjF0"></script>
<script>
    grecaptcha.ready(function () {
        grecaptcha.execute('6LdzBa8qAAAAAOgI_b-3adrulv5ak-NfVukmYjF0', { action: 'subscribe' })
            .then(function (token) {
                // Ensure the element exists before trying to set its value
                const recaptchaResponseElement = document.getElementById('g-recaptcha-response');
                if (recaptchaResponseElement) {
                    recaptchaResponseElement.value = token;
                } else {
                    console.warn("Element with ID 'g-recaptcha-response' not found. reCAPTCHA token may not be submitted.");
                    // You might want to dynamically add a hidden input if it's missing or alert the user
                    const form = document.querySelector('form[action="{{ route('login') }}"]');
                    if (form && !recaptchaResponseElement) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'g-recaptcha-response';
                        hiddenInput.id = 'g-recaptcha-response';
                        hiddenInput.value = token;
                        form.appendChild(hiddenInput);
                    }
                }
            });
    });
</script>

@endsection
