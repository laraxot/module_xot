<div class="mb-4">
    <x-card tpl="v1" {{--  class="bg-gradient-info" --}}>
        <x-slot name="title">
            <i class="fas fa-th mr-1"></i> Welcome
        </x-slot>
        <x-slot name="tools">
            <button type="button" class="btn bg-info btn-sm" data-card-widget="collapse">
				<i class="fas fa-minus"></i>
			</button>
				<button type="button" class="btn bg-info btn-sm" data-card-widget="remove">
				<i class="fas fa-times"></i>
			</button>
        </x-slot>
        <x-slot name="txt">
           <img class="img-fluid"
                        src="{{ Theme::asset('xot::assets/img/illustrations/at-work.svg') }}"
                        style="max-width: 26rem" />
        </x-slot>
    </x-card>
</div>
