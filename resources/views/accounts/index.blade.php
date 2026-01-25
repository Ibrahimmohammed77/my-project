@extends('layouts.app')

@section('title', 'Accounts')

@section('header')
<div class="row g-2 align-items-center">
  <div class="col">
    <div class="page-pretitle">Identity</div>
    <h2 class="page-title">Accounts</h2>
  </div>
  <div class="col-auto ms-auto d-print-none">
    <a href="{{ route('accounts.create') }}" class="btn btn-primary">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
      Create Account
    </a>
  </div>
</div>
@endsection

@section('content')
<div class="card">
  <div class="table-responsive">
    <table class="table table-vcenter table-mobile-md card-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Username</th>
          <th>Contact</th>
          <th>Status</th>
          <th class="w-1">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($accounts as $account)
        <tr>
          <td>
            <div class="d-flex py-1 align-items-center">
              @if($account->profile_image)
              <span class="avatar me-2" style="background-image: url({{ $account->profile_image }})"></span>
              @else
              <span class="avatar me-2">{{ substr($account->full_name, 0, 2) }}</span>
              @endif
              <div class="flex-fill">
                <div class="font-weight-medium">{{ $account->full_name }}</div>
                <div class="text-muted"><a href="#" class="text-reset">{{ $account->email }}</a></div>
              </div>
            </div>
          </td>
          <td>{{ $account->username }}</td>
          <td>{{ $account->phone }}</td>
          <td>
            @if($account->status)
            <span class="badge bg-blue text-blue-fg">{{ $account->status->name }}</span>
            @else
            <span class="badge bg-secondary">Unknown</span>
            @endif
          </td>
          <td>
            <div class="btn-list flex-nowrap">
              <a href="{{ route('accounts.edit', $account->account_id) }}" class="btn btn-white">
                Edit
              </a>
              <form action="{{ route('accounts.destroy', $account->account_id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                  </button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
