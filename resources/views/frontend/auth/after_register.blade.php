@extends('frontend.layouts.app')

@section('content')
<style>
    .background-image {
        background: linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.9)), url("{{ asset('assets/img/background.jpg') }}") center/cover no-repeat;
    }
    .logo {
        max-width: 150px;
    }
    .small-text {
        font-size: 0.9rem;
        color: #000000;
    }
</style>

<div id="login-page" class="container-fluid mt-2 pt-1">
    <div class="row min-vh-100">
        <!-- Colonne gauche avec image et texte -->
        <div class="col-lg-7 d-flex align-items-center justify-content-center background-image mt-5">
            <div class="text-center">
                <img src="{{ asset('assets/img/20210406125513!Armoiries_du_Togo (1).png') }}" alt="Armoiries du Togo" class="img-fluid mb-3 logo">
                <h2 class="mb-3">Plateforme de Suivi des Réformes au Togo</h2>
                <p class="small-text">Propriété du Ministère de l'Economie et des Finances / Secrétariat Permanent pour le Suivi des Politiques de Réformes et des Programmes Financiers</p>
            </div>
        </div>

        <!-- Colonne droite -->
        <div class="col-lg-5 d-flex align-items-center justify-content-center">
            <div class="w-75 text-center">
                <h4 class="mb-4">{{ __('Inscription réussie !') }}</h4>
                
                <div class="alert alert-success">
                    <p>Votre compte a été créé avec succès.</p>
                    <p>Un administrateur va examiner votre demande et l'activer dans les plus brefs délais.</p>
                </div>

                <div class="mt-4">
                    <a href="{{ route('home') }}" class="btn btn-primary">{{ __('Retour à l\'accueil') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection