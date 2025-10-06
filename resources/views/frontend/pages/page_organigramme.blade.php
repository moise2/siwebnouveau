@extends('frontend.layouts.page', [
    'pageTitle' => 'organigramme',
    'pageId' => 'articles'
])

@section('page-content')

<div class="row justify-content-center">
  
    <div class="text-center" style="height: 600px; display: flex; justify-content: center; align-items: center;">
        <img src="{{ asset('images/organigramme.png') }}" alt="Organigramme" class="img-fluid" style="max-height: 100%; object-fit: contain;">
    </div>
</div>



@endsection
