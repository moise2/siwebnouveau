<div id="actualitespprpf" class="row">
    <div class="col-12 mb-5 text-center">
        <h2 class="section-title">Actualités du SP-PRPF</h2>
        <p class="section-subtitle">Découvrez les dernières actualités et publications du SP-PRPF.</p>
    </div>

    <div class="col-lg-8 postsArea">
        <div class="row">
            @foreach ($articles as $article)
                <div class="col-md-6">
                    <div class="news-card">
                        {{-- On vérifie si l'image existe avant de l'utiliser --}}
                        @php
                            $imageUrl = $article->image ? asset('storage/' . $article->image) : 'url_de_votre_image_par_defaut.jpg';
                        @endphp
                            <a href="{{ route('articles.show', ['slug' => $article->slug]) }}"
                            class="news-link"
                            style="background-image: url('{{ $article->image_url }}');">
                            <div class="news-category">
                                {{-- Votre code ici est déjà robuste grâce à optional() --}}
                                {{ optional($article->categories->first())->name ?? 'Sans Catégorie' }}
                            </div>
                            <div class="news-overlay">
                                <div class="news-title" style="font-weight: 200;">{{ $article->title }}</div>
                                <div class="news-date">
                                    {{-- CORRECTION CRUCIALE : On vérifie si la date existe avant de la formater --}}
                                    @if ($article->published_at)
                                        Publié le {{ \Carbon\Carbon::parse($article->published_at)->format('d/m/Y') }}
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center">
            <a href="{{ route('articles.index') }}" class="btn btn-primary mt-4">Consulter nos actualités</a>
        </div>
    </div>

    <div class="col-lg-4 docsArea">

    @if($publications->isEmpty())
    <p>Aucune publication trouvée.</p>
@endif

@foreach($publications as $publication)
    <div class="publication-card">
        <div class="publication-overlay">
            <div class="publication-category">
            @if($publication->categories && $publication->categories->isNotEmpty())
                @foreach($publication->categories as $category)
                  {{ $category->name }}
                @endforeach
            @else
                <span>Aucune catégorie</span>
            @endif
            </div>
        </div>
        <div class="publication-info">
            <!-- <span class="publication-date">Publié le {{ $publication->date_publication }}</span> -->
            <span class="publication-date">Publié le {{ $publication->date_publication->translatedFormat('j F Y') }}</span>
            <span class="publication-title">{{ $publication->title }}</span>

            {{-- La route est déjà correcte ici --}}
            {{-- Suppression de target="_blank" pour un téléchargement direct --}}
            <a href="{{ route('documents.download.home', $publication->id) }}" class="publication-button">
                {{-- On appelle simplement l'attribut virtuel 'icon_class' --}}
                <i class="fas {{ $publication->icon_class }}"></i>

                {{-- Conversion de la taille en Mo (ou Ko si c'est plus pertinent) --}}
                @php
                    $sizeInKb = $publication->file_size / 1024;
                    $displaySize = ($sizeInKb > 1024) 
                        ? round($sizeInKb / 1024, 2) . ' Mo' 
                        : round($sizeInKb, 1) . ' Ko';
                @endphp
                <span>{{ $displaySize }}</span>

                <i class="fas fa-download"></i>
            </a>
            <span class="download-count">{{ $publication->download_count }} téléchargements</span>
        </div>
    </div>
@endforeach


        <div class="text-center">
            <a href="{{ route('documents.general.index') }}" class="btn btn-primary mt-4">Consulter tous les documents</a>
        </div>
    </div>
</div>