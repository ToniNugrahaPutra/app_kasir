@extends('layouts.main')

@section('container')

<div class="col-12 ">
    <div class="app-card app-card-account shadow-sm d-flex flex-column align-items-start">
        <div class="app-card-header p-3 border-bottom-0">
            <div class="row align-items-center gx-3">
                <div class="col-auto">
                    <div class="app-icon-holder" style="background-size: cover; background-image: url('<?= asset('storage/profile/'.$user->picture) ?>'); background-position: center;">

                    </div>

                </div>
                <div class="col-auto">
                    <h4 class="app-card-title">Profile</h4>
                </div>
            </div>
        </div>
        <div class="app-card-body px-4 w-100">
            <div class="item border-bottom py-3">
                <div class="row justify-content-between align-items-center">
                    <div class="col-auto">
                        <div class="item-label"><strong>Name</strong></div>
                        <div class="item-data">{{ $user->name }}</div>
                    </div>
                </div>
            </div>
            <div class="item border-bottom py-3">
                <div class="row justify-content-between align-items-center">
                    <div class="col-auto">
                        <div class="item-label"><strong>Email</strong></div>
                        <div class="item-data">{{ $user->email }}</div>
                    </div>
                </div>
            </div>
            <div class="item border-bottom py-3">
                <div class="row justify-content-between align-items-center">
                    <div class="col-auto">
                        <div class="item-label"><strong>Username</strong></div>
                        <div class="item-data">
                            {{ $user->username }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="item border-bottom py-3">
                <div class="row justify-content-between align-items-center">
                    <div class="col-auto">
                        <div class="item-label"><strong>Role</strong></div>
                        <div class="item-data">
                            {{ $user->getRoleNames()->first() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-card-footer p-4 mt-auto">
            <a class="btn app-btn-secondary" href="/user/edit/{{ $user->id }}">Edit Profile</a>
        </div>
    </div>
</div>

<div class="col-12 col-md-6 mt-4">
    <div class="app-card app-card-account shadow-sm d-flex flex-column align-items-start">
        <div class="app-card-header p-3 border-bottom-0">
            <div class="row align-items-center gx-3">
                <div class="col-auto">
                    <h4 class="app-card-title">QRIS Payment</h4>
                </div>
            </div>
        </div>
        <div class="app-card-body px-4 w-100 mb-3">
            <div class="row justify-content-center">
                <div class="col-12 text-center">
                    @if($user->outlets->first() && $user->outlets->first()->qris)
                        <img src="{{ asset('storage/qris/' . $user->outlets->first()->qris) }}" alt="QRIS" class="img-fluid" style="max-width: 300px">
                    @else
                        <p>No QRIS image uploaded</p>
                    @endif
                </div>
                @if($user->hasRole('owner'))
                <div class="col-12 mt-3">
                    <form action="{{ route('outlets.qris') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="qris" class="form-label">Upload QRIS Image</label>
                            <input type="file" class="form-control" id="qris" name="qris" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn app-btn-primary">Upload QRIS</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
