@extends('layouts.app')

@section('title', 'Edit Account')

@section('header')
<div class="row g-2 align-items-center">
  <div class="col">
    <div class="page-pretitle">Accounts</div>
    <h2 class="page-title">Edit Account: {{ $account->username }}</h2>
  </div>
</div>
@endsection

@section('content')
<div class="row row-cards">
  <div class="col-12">
    <form action="{{ route('accounts.update', $account->account_id) }}" method="POST" class="card">
      @csrf
      @method('PUT')
      <div class="card-header">
        <h4 class="card-title">Account Details</h4>
      </div>
      <div class="card-body">
         @include('accounts.form')
      </div>
      <div class="card-footer text-end">
        <div class="d-flex">
          <a href="{{ route('accounts.index') }}" class="btn btn-link">Cancel</a>
          <button type="submit" class="btn btn-primary ms-auto">Update Account</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
