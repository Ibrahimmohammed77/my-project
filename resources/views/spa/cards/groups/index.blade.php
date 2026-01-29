@extends('layouts.app')

@section('content')
    <h1>Card Groups</h1>
    <ul>
        @foreach($groups as $group)
            <li>{{ $group->name }}</li>
        @endforeach
    </ul>
@endsection
