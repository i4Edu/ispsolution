@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('OLT/ONU Sync') }}</div>

                    <div class="card-body">
                        <div class="alert alert-warning" role="alert">
                            {{ __('The OLT/ONU sync needs to be performed manually. Please contact the administrator.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
