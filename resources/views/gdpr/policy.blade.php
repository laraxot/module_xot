<x-layouts.guest>
    <div class="row justify-content-center pt-4">
        <div class="col-6">
            <div>
                <x-auth.card-logo />
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    {!! $policy !!}
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>
