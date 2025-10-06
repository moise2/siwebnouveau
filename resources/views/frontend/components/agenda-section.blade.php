<!-- resources/views/frontend/components/agenda-section.blade.php -->
<div id="agendaboss" class="container d-flex justify-content-center align-items-center my-5">
    <div>
        <div id="cartographie" class="row">
            <a href="{{ route('cartographie.index') }}" class="btn btn-primary d-flex align-items-center gap-2" target="_blank">
                <i class="fas fa-map"></i> Cartographie des activités
            </a>
        </div>
    </div>
</div>

<div id="contenuagendahome" class="col">
    <!-- Agenda Title -->
    <div class="agenda-title text-center mb-4">Agenda</div>

    <!-- Agenda Events Grid -->
    <div class="row agenda-container">
        @foreach($events as $event)
            <div class="col-md-4 mb-4">
                <div class="agenda-card card h-100 shadow-sm border-0">
                    <a href="{{ route('events.show', ['slug' => $event->slug]) }}">
                        <img src="{{ Storage::url($event->featured_image) }}" class="card-img-top" style="max-height: 180px; object-fit: cover;" alt="{{ htmlspecialchars($event->title, ENT_QUOTES, 'UTF-8') }}">
                    </a>
                    <div class="card-body p-3">
                        <a href="{{ route('events.show', ['slug' => $event->slug]) }}" class="text-decoration-none text-dark">
                            <h5 class="card-title mb-2" style="font-size: 0.85rem;">{{ htmlspecialchars($event->title, ENT_QUOTES, 'UTF-8') }}</h5>
                        </a>
                        <p class="agenda-card-period text-muted mb-1" style="font-size: 0.75rem;">
                            Début: {{ \Carbon\Carbon::parse($event->start_date)->format('d F Y') }}<br>
                            Fin: {{ \Carbon\Carbon::parse($event->end_date)->format('d F Y') }}
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Centered View More Button -->
    <div class="d-flex justify-content-center mt-4">
        <a href="{{ route('agenda') }}" 
        class="btn view-more-btn px-5 py-3" 
        style="background-color: #dc3545; color: #fff; font-size: 17px !important;">
            <i class="fas fa-calendar-alt" style="color: #fff !important;"></i> Voir plus
        </a>
    </div>


</div>

<!-- Custom Styles -->
<style>
    #contenuagendahome .agenda-title {
        font-size: 1.5rem;
        font-weight: bold;
    }

    .agenda-card img {
        border-radius: 0.5rem;
    }

    .view-more-btn {
        font-size: 0.875rem;
        padding: 6px 12px;
    }
</style>
