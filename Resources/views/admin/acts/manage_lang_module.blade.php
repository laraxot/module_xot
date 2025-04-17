@extends('adm_theme::layouts.app')
@section('content')
    @livewire('manage_lang_module', ['module_name' => $module_name])
@endsection
