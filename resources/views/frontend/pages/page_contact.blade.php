@extends('frontend.layouts.page', [
    'pageTitle' => 'Contact',
    'pageId' => 'articles'
])

@section('page-content')

<div class="row justify-content-center">
    <!-- Formulaire de contact -->
    <div class="col-lg-7 mb-5">
        <div class="contact-card p-4 shadow-sm rounded">
            <h2 class="card-title text-center mb-4">Formulaire de Contact</h2>

            <!-- Zone d'affichage des messages -->
            <div id="responseMessage" class="mb-4" style="display: none;"></div>

            <form id="contactForm" action="{{ route('contact.submit') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nom</label>
                    <input type="text" class="form-control form-control-lg rounded" id="name" name="name" placeholder="Votre nom" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control form-control-lg rounded" id="email" name="email" placeholder="Votre email" required>
                </div>
                <div class="mb-3">
                    <label for="subject" class="form-label">Sujet</label>
                    <input type="text" class="form-control form-control-lg rounded" id="subject" name="subject" placeholder="Sujet de votre message" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control form-control-lg rounded" id="message" name="message" rows="5" placeholder="Votre message" required></textarea>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill">Envoyer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Informations de contact et Google Maps -->
    <div class="col-lg-4">
        <div class="contact-card p-4 shadow-sm rounded bg-light mb-4">
            <h2 class="card-title text-center mb-4">Nous Contacter</h2>
            <div class="contact-info">
                <p><i class="fas fa-map-marker-alt"></i> Centre administratif des Services économiques et financiers - CASEF, Lomé</p>
                <p><i class="fas fa-phone-alt"></i> +228 91210176 </p>
                <p><i class="fas fa-envelope"></i> spreformetg@gmail.com</p>
            </div>
        </div>
        <div class="map-container">
            <!-- Intégration de Google Maps -->
            <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15868.075206388423!2d1.2174121!3d6.1281719!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1023e126af11f327%3A0xa1de8de790731d!2sTogoreforme%20(SP-PRPF)!5e0!3m2!1sfr!2stg!4v1727100778033!5m2!1sfr!2stg"
                width="417" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</div>

<!-- Script JavaScript pour gérer l'affichage des messages -->
<script>
    document.getElementById('contactForm').addEventListener('submit', function(event) {
        event.preventDefault();
        let formData = new FormData(this);

        fetch("{{ route('contact.submit') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Message de succès
            if (data.success) {
                displayMessage('success', data.success);
                document.getElementById('contactForm').reset();
            } else {
                // Affiche les erreurs
                displayMessage('danger', 'Une erreur est survenue. Veuillez réessayer.');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            displayMessage('danger', 'Une erreur est survenue lors de l\'envoi du message.');
        });
    });

    // Fonction pour afficher les messages avec effet de transition
    function displayMessage(type, message) {
        const messageContainer = document.getElementById('responseMessage');
        messageContainer.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                                        ${message}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                      </div>`;
        messageContainer.style.display = 'block';
        setTimeout(() => {
            messageContainer.style.display = 'none';
        }, 5000);  // Cache le message après 5 secondes
    }
</script>


<script src="https://www.google.com/recaptcha/api.js?render=6LdzBa8qAAAAAOgI_b-3adrulv5ak-NfVukmYjF0"></script>
<script>
    grecaptcha.ready(function () {
        grecaptcha.execute('6LdzBa8qAAAAAOgI_b-3adrulv5ak-NfVukmYjF0', { action: 'subscribe' })
            .then(function (token) {
                document.getElementById('g-recaptcha-response').value = token;
            });
    });
</script>

@endsection
