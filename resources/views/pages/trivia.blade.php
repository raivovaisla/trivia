@extends('layouts.layout')

@section('content')
    @if(config('app.debug'))
        @dump(session()->all())
    @endif
    @if(!session()->has('trivia'))
        @include('includes.start_form')
    @else
        @include('includes.question_form')
        @if($gameOver)
            @include('includes.reset_form')
        @endif
    @endif
@endsection