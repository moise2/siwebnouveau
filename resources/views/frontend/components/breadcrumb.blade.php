<div class="container my-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <!-- Vérification si la page actuelle est la page d'accueil -->
            <li class="breadcrumb-item {{ Request::is('/') ? 'active' : '' }}">
                <a href="{{ route('home') }}">Accueil</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $currentPage }}</li>
        </ol>
    </nav>
</div>
