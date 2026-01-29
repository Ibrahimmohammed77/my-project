@extends('layouts.app')

@section('content')
    <h1>Lookups</h1>
    <ul>
        @foreach($masters as $master)
            <li>{{ $master->name }}</li>
        @endforeach
    </ul>
@endsection
