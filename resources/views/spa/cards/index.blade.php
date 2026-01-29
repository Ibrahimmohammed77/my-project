@extends('layouts.app')

@section('content')
    <h1>Cards for {{ $group->name }}</h1>
    <ul>
        @foreach($cards as $card)
            <li>{{ $card->card_number }}</li>
        @endforeach
    </ul>
@endsection
