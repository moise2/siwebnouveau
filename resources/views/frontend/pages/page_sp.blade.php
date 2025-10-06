@extends('frontend.layouts.page', [
    'pageTitle' => __('Secrétaire Permanent'),
    'pageId' => 'articles'
])

@section('page-content')

<section class="container my-5">
    <div class="row align-items-center">
        <!-- Image du Secrétaire Permanent -->
        <div class="col-lg-4 text-center mb-4 mb-lg-0">
            <div class="image-container">
                <img src="{{ asset('assets/img/sp.png') }}" alt="Affo Tchichi DEDJI"
                     class="img-fluid rounded-circle shadow-lg"
                     style="width: 450px; height: 400px; object-fit: cover;" />
            </div>
        </div>

        <!-- Titre et description -->
        <div class="col-lg-8">
            <h1 class="mb-3 display-5 font-weight-bold">{{ __('Affo Tchichi DEDJI') }}</h1>
            <h2 class="mb-4 text-muted">{{ __('custom.secretaire_permanent') }}</h2>

            <p class="lead">
                {{__('custom.description_secretaire_permanent')}}
            </p>
            <p>
                {{__('custom.mission_secretaire_permanent')}}
            </p>
            <p>
                {{__('custom.collaboration_secretaire_permanent')}}
            </p>
        </div>
    </div>
</section>

@endsection
