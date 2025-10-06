@extends('frontend.layouts.app')

@section('title', 'Page d\'accueil - Togoreforme')

@section('content')


     @include('frontend.components.hero-slider')

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12 col-sm-12 mx-auto">
                @include('frontend.components.news-section')
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-12 col-sm-12 mx-auto">
                @include('frontend.components.key-figures')
            </div>
        </div>


        
    </div>

    <div class="container-fluid mt-5">
        <div class="row">
            <div class="col-12">
                @include('frontend.components.agenda-section')
            </div>
        </div>
        
    </div>



    <div id="animated-separator" style="
            width: 0;
            height: 2px;
            background-color: red;
            margin: 20px 0;
            transition: width 2s ease-out;">
        </div>

        <script>
        document.addEventListener("DOMContentLoaded", function () {
            const separator = document.getElementById("animated-separator");
            separator.style.width = "100%";
        });
        </script>

    <div style="display: flex; flex-wrap: wrap; max-width: 1200px; margin: 20px auto; gap: 20px; font-family: Arial, sans-serif; background-color:rgb(255, 255, 255);">
   


    <!-- Première Colonne : Dernier Tweet -->
    <div style=" margin-top:100px;margin-bottom:70px; flex: 1; min-width: 300px; background: #252525; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px;">
    <div style="display: flex; flex-direction: column; align-items: center;">
      <img src="{{ Storage::url('images/t.png')  }}" alt="Twitter Logo" style="width: 50px; margin-bottom: 10px;">
      <p id="last-tweet" style="font-size: 14px; color: #555; text-align: center;">
        @if($lastTweet)
        <div class="card mb-4" style="max-width: 600px; margin: auto; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <div class="card-body">
                <h5 class="card-title">@togoreforme</h5>
                <p class="card-text">
                    {{ is_object($lastTweet) ? json_decode('"' . $lastTweet->text . '"') : $lastTweet }}
                </p>

                {{-- Lien vers la page Twitter de Togoreforme --}}
                <p class="text-muted">
                    <strong>Page Twitter de Togoreforme :</strong>
                    <a href="https://x.com/togoreforme" target="_blank">
                        Visitez le profil Twitter
                    </a>
                </p>

                {{-- Affichage des likes --}}
                <div class="d-flex justify-content-start text-muted mt-3">
                    <span>
                        <i class="fas fa-heart"></i> 
                        {{ $lastTweet->favorite_count ?? 10 }} {{-- Si le nombre de likes est inexistant, on affiche 10 --}}
                    </span>
                </div>

                {{-- Affichage de l'image attachée au tweet, si elle existe --}}
                @if(isset($lastTweet->entities->media) && count($lastTweet->entities->media) > 0)
                    <div class="mt-3">
                        <img src="{{ $lastTweet->entities->media[0]->media_url_https }}" alt="Image du tweet" class="img-fluid">
                    </div>
                @endif
            </div>
        </div>
        @else
            <p>Aucun tweet trouvé.</p>
        @endif

    </div>
    </div>
  
    <!-- Seconde Colonne : Abonnement -->
    <div style="margin-top: 100px; margin-bottom: 70px; max-width: 400px; background: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px; margin-left: auto; margin-right: auto;">
    <div style="text-align: center;">
        <!-- Icône d'abonnement -->
        <div style="margin-bottom: 15px; color: red; font-size: 40px;">
            <i class="fas fa-bell"></i>
        </div>
        <h3 style="margin-bottom: 15px;">Abonnez-vous à notre point de presse</h3>
        <p style="font-size: 14px; color: #555;">
            Inscrivez-vous dès maintenant pour rester informé en temps réel des dernières publications.
        </p>
        
        <!-- Message de succès -->
        @if(session('success'))
            <div class="alert alert-success text-center mb-3">{{ session('success') }}</div>
        @endif

        <form id="subscribe-form" action="{{ route('subscriber.subscribe') }}" method="POST" style="display: flex; flex-direction: column; gap: 10px;">
            @csrf  

            <!-- Champ email -->
            <input 
                type="email" 
                name="email" 
                placeholder="Votre email" 
                required 
                style="padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; width: 100%;"
            >
            @error('email')
                <div class="text-danger text-center">{{ $message }}</div>
            @enderror

            <!-- Champ reCAPTCHA caché -->
            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

            <!-- Bouton d'envoi -->
            <button 
                type="submit" 
                style="padding: 12px; background-color: red; color: #fff; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; transition: background-color 0.3s;"
                onmouseover="this.style.backgroundColor='#cc0000'" 
                onmouseout="this.style.backgroundColor='red'"
            >
                S'abonner
            </button>
        </form>
    </div>
</div>

<!-- Script reCAPTCHA -->
<script src="https://www.google.com/recaptcha/api.js?render=6LdzBa8qAAAAAOgI_b-3adrulv5ak-NfVukmYjF0"></script>
<script>
    grecaptcha.ready(function () {
        grecaptcha.execute('6LdzBa8qAAAAAOgI_b-3adrulv5ak-NfVukmYjF0', { action: 'subscribe' })
            .then(function (token) {
                document.getElementById('g-recaptcha-response').value = token;
            });
    });
</script>

    <!-- Notifications -->

   <div id="notification-popup" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; max-width: 250px;">
    @foreach ($notifications as $notification)
        <div class="popup-card" data-id="{{ $notification->id }}" style="display: none; background:rgb(3, 0, 76); color: #fff; border: 1px solidrgb(22, 22, 23); border-radius: 8px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2); padding: 15px; margin-bottom: 10px; position: relative; font-family: Arial, sans-serif;">
            @if (!empty($notification->image))
                <img src="{{ Storage::url($notification->image) }}" alt="Notification Image" style="width: 100%; height: auto; border-radius: 5px; margin-bottom: 10px;">
            @endif
            <h5 style="margin: 0; font-size: 14px; font-weight: bold;">{{ $notification->title }}</h5>
            <p style="font-size: 12px; margin: 5px 0;">{{ $notification->message }}</p>
            <span class="close-popup" style="position: absolute; top: 8px; right: 8px; font-size: 16px; color: #fff; cursor: pointer; line-height: 1;">&times;</span>
        </div>
    @endforeach
   </div>


    <script>
        document.addEventListener("DOMContentLoaded", function () {
        const popups = document.querySelectorAll(".popup-card");
        let index = 0;

        function showNextPopup() {
            if (index < popups.length) {
                const popup = popups[index];
                popup.style.display = "block";
                index++;

                // Cache le popup après 7 secondes
                setTimeout(() => {
                    popup.style.display = "none";
                    showNextPopup();
                }, 7000);
            }
        }

        // Ajouter un événement pour fermer les popups manuellement
        document.querySelectorAll(".close-popup").forEach((btn) => {
            btn.addEventListener("click", (e) => {
                const popup = e.target.closest(".popup-card");
                popup.style.display = "none";
            });
        });

        // Démarrez le premier popup
        showNextPopup();
        });

    </script>


  
</div>


@endsection
{{-- @extends('frontend.layouts.app') --}}


