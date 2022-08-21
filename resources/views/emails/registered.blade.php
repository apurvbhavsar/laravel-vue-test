@extends('emails.layouts.app')

@section('title', 'You are registerd successfully')

@section('body')
    <p>
        Hello, {{ $name }}
        You are registerd successfully.
        Your account has been created.
    </p>
@endsection
