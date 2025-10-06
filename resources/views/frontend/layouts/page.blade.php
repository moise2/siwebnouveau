@extends('frontend.layouts.app')

@section('title', $pageTitle ?? 'Page - Togoreforme')

@section('content')
    <div id="{{ $pageId ?? 'page-content' }}">
        <!-- Blue Band Section -->
        <div class="blue-band">
            <div class="container">
                <h3 class="text-white">{{ $pageTitle }}</h3>
                <br>
                <br>
                @include('frontend.components.breadcrumb', ['currentPage' => $pageTitle])
            </div>
        </div>

        <!-- Card Section -->
        <div class="container mt-4">
            
                <div class="card-body">
                    @yield('page-content')
                </div>
            
        </div>

        {{-- Uncomment the following section if needed --}}
        {{--
        <div class="container content-container">
            <div class="row">
                <div class="col-12 mx-auto">
                    <div class="card presentation-card overlapping-card mt-n5 mb-5">
                        <div class="card-body">
                            @yield('page-content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        --}}
    </div>
@endsection
