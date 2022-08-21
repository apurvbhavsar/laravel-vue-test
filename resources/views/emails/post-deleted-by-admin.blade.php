@extends('emails.layouts.app')

@section('title', 'You are registerd successfully')

@section('body')
    <p>
        Hello, {{ $post->user->name }}.
        You post {{ $post->title }} has been deleted by Admin.
    </p>
@endsection
