@extends('frontend.layouts.app')

@section('title', 'Inscription') {{-- Définit le titre de la page pour le navigateur --}}

@section('content')
<style>
    body {
        background-color: #f0f2f5; /* Light grey background for the entire page */
    }
    .background-image {
        /* Dégradé bleu vers vert qui couvrira le drapeau du bas vers le haut */
        /* La première couleur est le dégradé, la seconde est l'image de fond (le drapeau) */
        background: linear-gradient(to top, rgba(2, 158, 140, 0.9), rgba(2, 69, 100, 0.7)), /* Opacités ajustées pour MIEUX voir le drapeau */
                    url("{{ asset('assets/img/drapeautogolais.jpeg') }}") center/cover no-repeat; /* Chemin et format JPEG confirmés */
        
        background-size: cover;
        background-position: center;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff; /* Texte en blanc pour un bon contraste avec le dégradé */
        overflow: hidden; /* Cache tout ce qui dépasse */
    }

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
        font-weight: 700; /* Assuré que le poids de la police est 700 */
        font-size: 1.6rem; /* Diminution de la taille du titre h2 */
        margin-top: 20px;
    }
    .small-text {
        font-size: 1rem; /* Slightly larger text for better readability */
        color: #ffffff; /* Assuré que le texte est blanc */
        line-height: 1.6;
        padding: 0 20px; /* Add horizontal padding for text */
    }
    /* Styles personnalisés pour le message de session (pour les erreurs ou succès de soumission) */
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
    .registration-form-card { /* Renommé de login-form-card pour la cohérence */
        background-color: #ffffff; /* White background for the form card */
        padding: 40px; /* More padding inside the form card */
        border-radius: 1.5rem; /* More rounded corners for the card */
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); /* Deeper shadow for a floating effect */
        transition: all 0.3s ease-in-out;
    }
    .registration-form-card:hover {
        transform: translateY(-5px); /* Slight lift on hover */
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
    }
    .registration-form-card h3 { /* Cible le h3 du formulaire d'inscription */
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
    .login-link { /* Renommé de 'a' pour être plus spécifique */
        color: #007bff;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }
    .login-link:hover {
        color: #0056b3;
        text-decoration: underline;
    }

    /* Ajustements responsive */
    @media (max-width: 991.98px) { /* Pour les écrans plus petits que large (lg) */
        .col-lg-5.background-image { /* Masque la partie image sur mobile */
            display: none !important;
        }
        .col-lg-7 { /* Le formulaire prend toute la largeur sur mobile */
            width: 100%;
            flex: 0 0 100%;
            max-width: 100%;
        }
        .registration-form-card {
            padding: 2rem 1.5rem;
            border-radius: 0;
            box-shadow: none;
            margin-top: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        #registration-page {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }
    }
</style>

<div id="registration-page" class="container-fluid d-flex align-items-center justify-content-center min-vh-100 py-3">
    <div class="row w-100 flex-grow-1 justify-content-center">
        <!-- Colonne gauche avec image et texte (occupant 5 colonnes sur les grands écrans) -->
        <div class="col-lg-5 d-none d-lg-flex background-image">
            <div class="text-center content-wrapper">
                <img src="{{ asset('assets/img/20210406125513!Armoiries_du_Togo (1).png') }}" alt="Armoiries du Togo" class="img-fluid mb-4 logo">
                <h2 class="mb-3">Plateforme de Suivi des Réformes au Togo</h2>
                <p class="small-text">Propriété du Ministère de l'Economie et des Finances / Secrétariat Permanent pour le Suivi des Politiques de Réformes et des Programmes Financiers</p>
            </div>
        </div>

        <!-- Colonne droite avec le formulaire (occupant 7 colonnes sur les grands écrans) -->
        <div class="col-lg-7 d-flex align-items-center justify-content-center py-5">
            <div class="w-75 registration-form-card"> <!-- w-75 maintient une petite marge à droite -->
                <h3 class="mb-4 text-center">{{ __('Créer un Compte') }}</h3>
                
                @if ($errors->any())
                    <div class="alert alert-danger fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.ptf') }}">
                    @csrf

                    <!-- Conteneur pour les champs en deux colonnes -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">{{ __('Nom complet') }}</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role_id" class="form-label">{{ __('Rôle') }}</label>
                            <select class="form-control @error('role_id') is-invalid @enderror" 
                                    id="role_id" name="role_id" required onchange="loadInstitutions()">
                                <option value="">Sélectionnez un rôle</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="institution_id" class="form-label">{{ __('Institution') }}</label>
                            <select class="form-control @error('institution_id') is-invalid @enderror" 
                                    id="institution_id" name="institution_id" required>
                                <option value="">Sélectionnez une institution</option>
                                {{-- Les options seront chargées dynamiquement via JavaScript --}}
                            </select>
                            @error('institution_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">{{ __('Mot de passe') }}</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="password-confirm" class="form-label">{{ __('Confirmer le mot de passe') }}</label>
                            <input type="password" class="form-control" 
                                   id="password-confirm" name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2">{{ __("S'inscrire") }}</button>

                    <div class="mt-4 text-center">
                        <p class="mb-0">Vous avez déjà un compte ? <a href="{{ route('login') }}" class="login-link">Connectez-vous</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script reCAPTCHA -->
<script src="https://www.google.com/recaptcha/api.js?render=6LdzBa8qAAAAAOgI_b-3adrulv5ak-NfVukmYjF0"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    /**
     * Charge les institutions (bailleurs) en fonction du rôle sélectionné.
     * Effectue une requête de login via proxy pour obtenir un token, puis une requête pour les bailleurs.
     * @returns {Promise<void>}
     */
    async function loadInstitutions() {
        const roleSelect = document.getElementById('role_id');
        const institutionSelect = document.getElementById('institution_id');

        // Check if elements exist before proceeding
        if (!roleSelect || !institutionSelect) {
            console.error("loadInstitutions: Missing role_id or institution_id elements.");
            return;
        }

        const roleId = roleSelect.value;
        institutionSelect.innerHTML = '<option value="">Chargement...</option>'; // Message de chargement

        if (!roleId) {
            institutionSelect.innerHTML = '<option value="">Sélectionnez une institution</option>'; // Réinitialise si pas de rôle
            return;
        }

        console.log('Role ID sélectionné:', roleId);

        try {
            // Première étape : obtenir le token via notre proxy de login
            // ATTENTION : Faire un appel de login via proxy depuis le frontend pour obtenir un token
            // peut avoir des implications de sécurité si non géré correctement.
            // Assurez-vous que votre endpoint /proxy/login est sécurisé et approprié pour cet usage.
            console.log('Début de la requête de login pour obtenir le token...');
            const loginResponse = await axios.post('/proxy/login', {}, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            
            console.log('Réponse login:', loginResponse.data);
            const token = loginResponse.data.token; // Assurez-vous que votre API retourne un champ 'token'
            console.log('Token obtenu (partiel pour sécurité):', token ? token.substring(0, 10) + '...' : 'Aucun token');

            // Deuxième étape : obtenir les bailleurs via notre proxy avec le token
            console.log('Début de la requête pour les bailleurs...');
            const bailleurResponse = await axios.get('/proxy/bailleurs', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            console.log('Réponse bailleurs:', bailleurResponse.data);
            const bailleurData = bailleurResponse.data;

            // Mettre à jour le select des institutions
            institutionSelect.innerHTML = '<option value="">Sélectionnez une institution</option>'; // Réinitialise avant d'ajouter
            
            if (bailleurData && bailleurData.records && Array.isArray(bailleurData.records)) {
                bailleurData.records.forEach(bailleur => {
                    console.log('Traitement bailleur:', bailleur);
                    const option = document.createElement('option');
                    option.value = bailleur.id;
                    option.textContent = bailleur.nom;
                    institutionSelect.appendChild(option);
                });
            } else {
                console.warn('Format de données inattendu ou pas de records trouvés pour les bailleurs.');
                institutionSelect.innerHTML = '<option value="">Aucune institution disponible</option>';
            }

            console.log('Mise à jour du select terminée');

        } catch (error) {
            console.error('Erreur lors du chargement des institutions:', error);
            institutionSelect.innerHTML = '<option value="">Erreur de chargement des institutions</option>';
            if (error.response) {
                console.error('Détails de l\'erreur de la réponse:', error.response.data);
                console.error('Statut de l\'erreur:', error.response.status);
            }
        }
    }

    // Charger les institutions au chargement de la page si un rôle est déjà sélectionné (par exemple, après une erreur de validation)
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM chargé');
        const roleSelect = document.getElementById('role_id');
        if (roleSelect) { // Ensure roleSelect exists
            const roleId = roleSelect.value;
            console.log('Role ID initial au chargement:', roleId);
            if (roleId) {
                loadInstitutions();
            }
        } else {
            console.warn("DOMContentLoaded: role_id element not found.");
        }
    });
</script>
@endsection
