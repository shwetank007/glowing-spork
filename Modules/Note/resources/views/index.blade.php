@extends('note::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('note.name') !!}</p>
@endsection
