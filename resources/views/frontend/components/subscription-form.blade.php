<div id="sabonner" class="container my-5">
    <div class="row justify-content-center">
        <!-- Twitter Feed Section -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-lg rounded-4" style="background-color: #133353; max-width: 450px;">
                <div class="card-body p-4" style="max-height: 270px; overflow-y: auto;">
                    <div class="d-flex align-items-center mb-3">
                    <img src="logo-x.png" alt="Logo X" class="logo-x" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; filter: brightness(0) invert(1);">

                        <h5 class="ms-3 mb-0" style="font-family: 'Segoe UI', sans-serif; color: #ffffff; font-weight: 600;">Togoreformes</h5>
                    </div>
                    <div class="twitter-feed" style="max-width: 380px;">
                        <!-- Si le dernier tweet existe -->
                        @if($lastTweet)
                            <blockquote class="twitter-tweet mb-2" style="border-left: 4px solid #1DA1F2; padding-left: 12px; font-size: 14px; line-height: 1; color: #555;">
                                <p lang="en" dir="ltr" style="margin-bottom: 10px; overflow-wrap: break-word;">{{ $lastTweet->text }}</p>
                                <!-- Placeholder pour l'image du tweet -->
                                @if($lastTweet->image)
                                    <div style="max-width: 80%; margin-bottom: 5px;">
                                        <img src="{{ $lastTweet->image }}" alt="Image du tweet" style="width: 70%; height: 20% !important; border-radius: 8px;">
                                    </div>
                                @endif
                                &mdash; Twitter User (@togoreforme) <a href="https://twitter.com/togoreforme/status/{{ $lastTweet->id_twitt }}" class="text-decoration-none text-primary" style="font-weight: 400 !important ;font-size:10px!important">Voir sur Twitter</a>
                            </blockquote>
                            <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                        @else
                            <p class="text-muted">Aucun tweet trouvé.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Form Section -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-lg rounded-4" style="background-color: #ffffff;">
                <div class="card-body p-4">
                    <h5 class="card-title text-center mb-3" style="font-family: 'Segoe UI', sans-serif; color: #333; font-weight: 600;">Restez informés</h5>
                    <p class="text-center mb-4" style="font-size: 16px; color: #555;">Abonnez-vous au point de presse pour recevoir les dernières informations.</p>

                    @if(session('success'))
                        <div class="alert alert-success text-center mb-3">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('subscriber.subscribe') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control form-control-lg" placeholder="Votre email" required style="border-radius: 25px; font-size: 16px; padding: 15px;">
                            @error('email')
                                <div class="text-danger text-center mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-danger btn-lg w-100" style="border-radius: 25px; padding: 12px 0; font-size: 16px; background-color: #e02f2f; border: none;">S'abonner</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inclure le fichier JavaScript -->
<script src="{{ asset('js/fetchTweet.js') }}"></script>

<style>
    #sabonner .card {
        border: none;
        border-radius: 15px;
    }

    #sabonner .card-body {
        padding: 20px;
    }

    #sabonner .twitter-feed blockquote {
        font-size: 14px;
        line-height: 1.4;
        margin: 0;
        padding-left: 12px;
        word-wrap: break-word;
    }

    #sabonner .btn-danger {
        background-color: #e02f2f;
        border: none;
        font-weight: 600;
    }

    #sabonner .btn-danger:hover {
        background-color: #c72c2c;
    }

    /* Responsive Design */
    @media (max-width: 767px) {
        #sabonner .card-body {
            padding: 15px;
        }

        #sabonner .card-title {
            font-size: 18px;
        }

        #sabonner .twitter-feed blockquote {
            font-size: 13px;
            padding-left: 10px;
        }
    }
</style>
