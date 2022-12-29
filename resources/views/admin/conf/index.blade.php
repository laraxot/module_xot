@extends('adm_theme::layouts.app')
@section('page_heading', 'package Settings')
@section('content')
    <x-flash-message />


    @php
        //$rows = \Config::all();
    @endphp
    <ul class="nav nav-pills mb-3 nav-justified" id="pills-tab" role="tablist">
        @foreach ($rows as $k => $v)
            <li class="nav-item">
                <a class="nav-link btn " href="{{ url($v->url) }}">{{ $v->title }}</a>
            </li>
        @endforeach
    </ul>

@endsection
