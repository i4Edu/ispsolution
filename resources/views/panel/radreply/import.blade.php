@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Import RADIUS Reply Entries</h1>

    <form action="{{ route('radreply.import') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="file" class="form-label">CSV File (username,attribute,op,value)</label>
            <input type="file" name="file" id="file" class="form-control" accept=".csv">
        </div>
        <button class="btn btn-primary">Import</button>
    </form>
</div>
@endsection
