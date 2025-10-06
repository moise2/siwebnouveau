@extends('frontend.layouts.page', [
    'pageTitle' => 'Qui sommes-nous ?',
    'pageId' => 'qui-sommes-nous'
])

@section('page-content')
    @include('frontend.components.content-card')
    @include('frontend.components.value-cards')
    @include('frontend.components.cta-buttons')
@endsection
