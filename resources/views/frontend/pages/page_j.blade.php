<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'Inscription</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css" rel="stylesheet">
    <style>
        .step {
            display: none;
        }

        .step.active {
            display: block;
        }

        .progress-bar {
            width: 0%;
        }

        .dragover {
            background-color: #f0f0f0;
            border: 2px dashed #ccc;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .error {
            color: red;
            font-size: 12px;
            display: none;
        }

        .is-invalid {
            border-color: red;
        }

        .progress {
            height: 30px;
            margin-bottom: 20px;
        }

        .container {
            max-width: 900px;
            margin-top: 50px;
        }

        .form-control {
            margin-bottom: 10px;
            width: 100%;
        }

        #dropzone {
            height: 75px;
            border: 2px dashed #ccc;
            text-align: center;
            padding-top: 20px;
            cursor: pointer;
        }

        #dropzone.dragover {
            background-color: #f0f0f0;
        }

        .header-img {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-img img {
            max-width: 100px;
        }

        .step h4 {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header-img">
            <img src="{{ asset('assets/img/20210406125513_Armoiries_du_Togo__1_-removebg-preview.png') }}" alt="Armorie du Togo">
            <h3>Formulaire d'Inscription</h3>
        </div>

        <!-- Messages de succès et d'erreur -->
        <div id="alert-container">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
        </div>

        <div class="progress">
            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>

        <form id="form" method="POST" action="{{ route('utilisateurs.store') }}">
            @csrf
            <!-- Étape 1 : Informations Personnelles -->
            <div class="step" id="step-1">
                <h4>Étape 1 : Informations Personnelles</h4>
                <!-- <div class="form-group">
                    <label for="image">Image de Profil :</label>
                    <div id="dropzone" class="form-control">Cliquez ici pour télécharger une image</div>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*" style="display: none;">
                    <div class="error" id="imageError">Veuillez télécharger une image valide.</div>
                    <div id="imagePreview"></div>
                </div> -->
                <div class="form-group">
                    <label for="nom">Nom <span style="color: red;">*</span> :</label>
                    <input type="text" class="form-control" id="nom" name="nom" placeholder="Entrez votre nom" required>
                    <div class="error" id="nomError">Veuillez entrer votre nom.</div>
                </div>
                <div class="form-group">
                    <label for="prenoms">Prénoms <span style="color: red;">*</span> :</label>
                    <input type="text" class="form-control" id="prenoms" name="prenoms" placeholder="Entrez vos prénoms" required>
                    <div class="error" id="prenomsError">Veuillez entrer vos prénoms.</div>
                </div>
                <div class="form-group">
                    <label for="role">Sexe <span style="color: red;">*</span> :</label>
                    <select class="form-control" id="sexe" name="sexe" required>
                        <option value="">Sélectionner le sexe</option>
                        <option value="Homme">Homme</option>
                        <option value="Femme">Femme</option>
                    </select>
                    <div class="error" id="roleError">Veuillez sélectionner le sexe.</div>
                </div>
                <button type="button" class="btn btn-primary btn-next">Suivant</button>
            </div>

            <!-- Étape 2 : Informations de Connexion -->
            <div class="step" id="step-2">
                <h4>Étape 2 : Informations de Connexion</h4>
                <div class="form-group">
                    <label for="email">Email <span style="color: red;">*</span> :</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Entrez votre email" required>
                    <div class="error" id="emailError">Veuillez entrer un email valide.</div>
                </div>
                <div class="form-group">
                    <label for="contact">Numéro de Contact <span style="color: red;">*</span> :</label>
                    <input type="tel" class="form-control" id="contact" name="contact" placeholder="Entrez votre numéro de contact" required>
                    <div class="error" id="contactError">Veuillez entrer un numéro de contact valide.</div>
                </div>
                <button type="button" class="btn btn-secondary btn-prev">Précédent</button>
                <button type="button" class="btn btn-primary btn-next">Suivant</button>
            </div>

            <!-- Étape 3 : Rôle et Institution -->
            <div class="step" id="step-3">
                <h4>Étape 3 : Rôle et Institution</h4>
                <div class="form-group">
                    <label for="role">Rôle <span style="color: red;">*</span> :</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="">Sélectionner un rôle</option>
                        <option value="CT">Correspondant Thématique</option>
                        <option value="PTF">Partenaire technique et financier</option>
                        <option value="PF">Point Focal</option>
                    </select>
                    <div class="error" id="roleError">Veuillez sélectionner un rôle.</div>
                </div>
                <div class="form-group">
                    <label for="institution_id">Institution <span style="color: red;">*</span> :</label>
                    <select class="form-control" id="institution_id" name="institution" required>
                        <option value="">Sélectionner une institution</option>
                        @foreach ($institutions as $institution)
                            <option value="{{ $institution->id }}">{{ $institution->libelle }}</option>
                        @endforeach
                    </select>
                    <div class="error" id="institution_idError">Veuillez sélectionner une institution.</div>
                </div>
                <button type="button" class="btn btn-secondary btn-prev">Précédent</button>
                <button type="button" class="btn btn-primary btn-next">Suivant</button>
            </div>

            <!-- Étape 4 : Créer votre mot de passe -->
            <div class="step" id="step-4">
                <h4>Étape 4 : Créer votre mot de passe</h4>
                <div class="form-group">
                    <label for="password">Mot de passe <span style="color: red;">*</span> :</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Entrez un mot de passe" required>
                    <div class="error" id="passwordError">Veuillez saisir un mot de passe.</div>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirmation <span style="color: red;">*</span> :</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirmez votre mot de passe" required>
                    <div class="error" id="confirmPasswordError">Veuillez confirmer votre mot de passe.</div>
                </div>
                <button type="button" class="btn btn-secondary btn-prev">Précédent</button>
                <button type="submit" class="btn btn-success">Soumettre</button>
            </div>
        </form>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
    let currentStep = 1;
    showStep(currentStep);

    // Gérer le bouton suivant
    $('.btn-next').click(function () {
        if (validateForm()) {
            currentStep++;
            showStep(currentStep);
        }
    });

    // Gérer le bouton précédent
    $('.btn-prev').click(function () {
        currentStep--;
        showStep(currentStep);
    });

    function showStep(step) {
        $('.step').removeClass('active');
        $('#step-' + step).addClass('active');
        updateProgressBar(step);
        $('.btn-prev').toggle(step > 1);
        $('.btn-next').text(step === 4 ? 'Soumettre' : 'Suivant');
    }

    function updateProgressBar(step) {
        const percentage = (step - 1) * 33.33;
        $('.progress-bar').css('width', percentage + '%');
    }

    function validateForm() {
        let isValid = true;
        $('#step-' + currentStep + ' input[required], #step-' + currentStep + ' select[required]').each(function () {
            const input = $(this);
            const id = input.attr('id');
            if (input.val() === '') {
                $('#' + id + 'Error').show();
                input.addClass('is-invalid');
                isValid = false;
            } else {
                $('#' + id + 'Error').hide();
                input.removeClass('is-invalid');
            }
        });

        // Validation spécifique pour le mot de passe et sa confirmation
        if (currentStep === 4) {
            const password = $('#password').val();
            const confirmPassword = $('#confirmPassword').val();
            if (password !== confirmPassword) {
                $('#confirmPasswordError').show();
                $('#confirmPassword').addClass('is-invalid');
                isValid = false;
            } else {
                $('#confirmPasswordError').hide();
                $('#confirmPassword').removeClass('is-invalid');
            }
        }

        return isValid;
    }
});

    </script>



</html>
