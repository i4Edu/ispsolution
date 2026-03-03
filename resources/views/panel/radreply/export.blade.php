@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Export RADIUS Reply Entries</h1>

    <p>
        <a href="{{ route('radreply.export') }}" class="btn btn-success">Download CSV</a>
    </p>
</div>
@endsection
