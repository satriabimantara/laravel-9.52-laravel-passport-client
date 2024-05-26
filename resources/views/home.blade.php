@extends('layouts.app')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                Timeline | {{ Auth::user()->name }}
            </div>
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                @if (Auth::user()->token)
                    @if ($tweets->count())
                        @foreach ($tweets as $tweet)
                        <div class="media">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <img src="https://placehold.it/64x64" alt="Generic Placeholder">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    {{ $tweet->body }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                @else
                <p>Please <a href="{{ route('oauth.passport') }}">authorize with passport</a> first!</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
