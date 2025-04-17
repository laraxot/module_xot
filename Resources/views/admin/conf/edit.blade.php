@extends('adm_theme::layouts.app')
@section('page_heading', $params['module'])
@section('content')
    <x-flash-message />
    @include('ui::modal_ajax')

    <div class="page-wrapper">
        {!! Theme::include('inner_page', [], get_defined_vars()) !!}
        @php
            /*

		*/
        @endphp
        <section class="create-page inner-page">
            <div class="container-fluid">
                {{-- dddx(get_defined_vars()) --}}
                {!! Form::bsOpen(null, 'update') !!}


                <div class="row">
                    @foreach ($row as $k => $v)
                        @if (is_array($v))
                            {{ Form::bsArray($k, $v) }}
                        @else
                            {{ Form::bsText($k, $v) }}
                        @endif
                    @endforeach
                </div>
                {{ Form::bsSubmit('Save') }}
                {{ Form::close() }}

            </div>
        </section>
    </div>
@endsection
