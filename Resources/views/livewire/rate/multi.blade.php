<div>
    @component('theme::components.modal.simple',['guid'=>$modal_guid,'title'=>$modal_title])
    @slot('content')
    AAAAAAAAAAAAAA
        @livewire('xot::rate.single',['model'=>$model ])



    @endslot
    @slot('btns')

    @endslot
    @endcomponent

    <button data-toggle="modal" data-target="#{{ $modal_guid }}"  class="btn btn-primary mb-2">
        Vota <i class="fas fa-star"></i>
    </button>

</div>

