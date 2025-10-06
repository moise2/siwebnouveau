@extends('frontend.layouts.app')

@section('title', $article->title)

@section('content')
<div id="custom-article">
    <div class="container-fluid">
        {{-- Image principale --}}
        <div class="blue-band">
            <div class="image-overlay-container">
                <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="featured-image">
                <div class="image-overlay-text">{!! html_entity_decode(e($article->title)) !!}</div>
                <div class="image-overlay-text" style="margin-top:550px;font-size: 10px !important;"><span class="red-line"></span>{{ optional($article->categories->first())->name }}</div>
            
            </div>
        </div>





        <div class="content-divider"></div>

        <div class="container article-content">
            <div class="row" >
                {{-- Contenu principal --}}
                <div class="col-md-9 right-column" style="font-size: 20px !important;">
                    <!-- <h1 class="article-title">{{ $article->title }}</h1> -->
                    <div class="underline"></div>
                    <p class="article-summary">
                        <em style="font-weight: 500;">{{ $article->meta_description }}</em>
                    </p>
                    <br>
                    @php
                            // Récupérer le contenu de l'article
                            $body = $article->body;

                            // Utiliser DOMDocument pour charger le contenu
                            libxml_use_internal_errors(true);
                            $doc = new \DOMDocument();
                            $doc->loadHTML(mb_convert_encoding($body, 'HTML-ENTITIES', 'UTF-8'));

                            // Créer un objet XPath pour rechercher des éléments spécifiques
                            $xpath = new \DOMXPath($doc);

                            // 1. Supprimer les icônes PDF (img avec class wpdm_icon)
                            $imgNodes = $xpath->query('//img[contains(@class, "wpdm_icon")]');
                            foreach ($imgNodes as $img) {
                                $img->parentNode->removeChild($img); // Supprimer l'élément img
                            }

                            // 2. Ajouter une icône de téléchargement dans les liens avec wpdmdl
                            $links = $xpath->query('//a[contains(@href, "wpdmdl")]');
                            foreach ($links as $link) {
                                // Vérifier si l'icône de téléchargement n'est pas déjà présente
                                if (strpos($link->nodeValue, 'Télécharger') !== false) {
                                    $icon = $doc->createElement('i');
                                    $icon->setAttribute('class', 'fas fa-download me-1');
                                    $link->insertBefore($icon, $link->firstChild); // Insérer l'icône avant le texte
                                }
                            }

                            // Nettoyage final pour ne pas afficher les balises <html> et <body>
                            $newBody = $doc->saveHTML();
                            $newBody = preg_replace('~^<!DOCTYPE.+?>~', '', $newBody);
                            $newBody = str_replace(['<html>', '</html>', '<body>', '</body>'], '', $newBody);
                @endphp

                        {!! $newBody !!}

                    @php
                        // Extraction des liens Facebook
                        preg_match_all('/https?:\/\/(?:www\.)?facebook\.com\/[^\s]+/', $article->body, $fbMatches);
                        $facebookUrls = $fbMatches[0];
                    @endphp

                    @if (!empty($facebookUrls))
                        <div class="facebook-links">
                            @foreach ($facebookUrls as $fbUrl)
                                <p><a href="{{ $fbUrl }}" target="_blank">{{ $fbUrl }}</a></p>
                            @endforeach
                        </div>
                    @endif

                    {{-- Extraction et affichage des liens Twitter --}}
                    @php
                        preg_match_all('/https?:\/\/t\.co\/[a-zA-Z0-9]+|@[\w]+/', $article->body, $matches);
                        $tweetUrls = $matches[0];
                    @endphp

                    @if (!empty($tweetUrls))
                        <div class="tweets">
                            @foreach ($tweetUrls as $tweetUrl)
                                <blockquote class="twitter-tweet">
                                    <a href="{{ trim($tweetUrl) }}"></a>
                                </blockquote>
                            @endforeach
                        </div>
                        <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                    @endif

                    <div class="nav-links">
                        @if ($previousArticle)
                            <a href="{{ route('articles.show', $previousArticle->slug) }}">&laquo; Article précédent</a>
                        @endif

                        @if ($nextArticle)
                            <a href="{{ route('articles.show', $nextArticle->slug) }}">Article suivant &raquo;</a>
                        @endif
                    </div>
                </div>

                {{-- Barre latérale --}}
                <div class="col-md-3 left-column">
                    <div class="article-meta">
                        <!-- <div class="category"><span class="red-line"></span>{{ optional($article->categories->first())->name }}</div> -->
                        <div class="meta">
                            <span style="font-weight: 700; color:rgb(157, 18, 2);">Date: {{ \Carbon\Carbon::parse($article->published_at)->format('d/m/Y') }} </span>
                            <span style="font-weight: 700; color:rgb(157, 18, 2);">Heure: {{ \Carbon\Carbon::parse($article->create_at)->locale('fr')->format('H : i') }}</span>
                        </div>
                    </div>
                    <div class="progress-container">
                        <div class="progress-bar" id="progressBar"></div>
                    </div>
                    <div class="social-icons">

                                <!-- Lien de partage Facebook -->
                                <a href="#" onclick="shareOnFacebook()" target="_blank" title="Partager sur Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>

                                <!-- Lien de partage LinkedIn -->
                                <a href="#" onclick="shareOnLinkedIn()" target="_blank" title="Partager sur LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>

                                <!-- Lien de partage Twitter -->
                                <a href="#" onclick="shareOnTwitter()" target="_blank" title="Partager sur Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>

                                <!-- Lien de partage WhatsApp -->
                                <a href="#" onclick="shareOnWhatsApp()" target="_blank" title="Partager sur WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>

                                <script>
                                    // Fonction pour partager sur Facebook
                                    function shareOnFacebook() {
                                        const currentUrl = window.location.href;  // Récupère l'URL actuelle
                                        const facebookUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(currentUrl)}`;
                                        window.open(facebookUrl, '_blank');
                                    }

                                    // Fonction pour partager sur LinkedIn
                                    function shareOnLinkedIn() {
                                        const currentUrl = window.location.href;
                                        const linkedInUrl = `https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(currentUrl)}`;
                                        window.open(linkedInUrl, '_blank');
                                    }

                                    // Fonction pour partager sur Twitter
                                    function shareOnTwitter() {
                                        const currentUrl = window.location.href;
                                        const twitterUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(currentUrl)}&text=Voici%20un%20article%20intéressant!`;
                                        window.open(twitterUrl, '_blank');
                                    }

                                    // Fonction pour partager sur WhatsApp
                                    function shareOnWhatsApp() {
                                        const currentUrl = window.location.href;
                                        const whatsappUrl = `https://wa.me/?text=Voici%20un%20article%20intéressant%20sur%20${encodeURIComponent(currentUrl)}`;
                                        window.open(whatsappUrl, '_blank');
                                    }
                                </script>

                        <div class="like-button" id="likeButton">
                            <i class="far fa-thumbs-up"></i><span id="likeCount">0</span>
                        </div>
                    </div>
                    <br>
                    <div class="similar-articles">
                        <span>Articles similaires</span>
                        @foreach ($relatedArticles as $relatedArticle)
                            <div class="news-card">
                                <a href="{{ route('articles.show', $relatedArticle->slug) }}" class="news-link" style="background-image: url('{{ asset('storage/' . $relatedArticle->image) }}');">
                                    <div class="news-category">{{ optional($relatedArticle->categories->first())->name }}</div>
                                    <div class="news-overlay">
                                        <div class="news-title">{{ html_entity_decode($relatedArticle->title) }}</div>
                                        <div class="news-date">{{ $relatedArticle->created_at->format('d/m/Y') }}</div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                        <a href="{{ route('articles.index') }}" class="btn btn-primary mt-4">Voir plus</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const likeButton = document.getElementById('likeButton');
        const likeCount = document.getElementById('likeCount');

        let liked = false;
        let count = parseInt(likeCount.textContent);

        likeButton.addEventListener('click', function() {
            if (!liked) {
                count++;
                likeButton.classList.add('liked');
            } else {
                count--;
                likeButton.classList.remove('liked');
            }
            likeCount.textContent = count;
            liked = !liked;
        });
    });

    document.addEventListener('scroll', function() {
        const progressBar = document.getElementById('progressBar');
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrollPercent = (scrollTop / scrollHeight) * 100;

        progressBar.style.width = scrollPercent + '%';
    });
</script>
