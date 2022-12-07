<x-theme::layouts.guest>
    <div class="row justify-content-center pt-4">
        <div class="col-6">
            <div>
                <x-theme::auth.card-logo />
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    {!! $terms !!}
                </div>
            </div>
        </div>
    </div>
</x-theme::layouts.guest>