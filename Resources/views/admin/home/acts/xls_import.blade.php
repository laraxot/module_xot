@extends('adm_theme::layouts.app')
@section('content')
    {{-- <div class="card bg-light mt-3">
        <div class="card-header">
            Import Excel to Database
        </div>
        <div class="card-body">
            <form action="{{ Request::fullUrl() }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" class="form-control">
                <br>
                <button class="btn btn-success">Next !</button>
            </form>
        </div>
    </div> --}}
    <livewire:import.xls.model modelClass="\Modules\User\Models\User" />
@endsection
