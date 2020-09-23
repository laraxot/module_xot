<div>

    <input wire:model="animal" name="animal" type="radio" value="cats" /> Cats
    <input wire:model="animal" name="animal" type="radio" value="dogs" /> Dogs
    <input wire:model="animal" name="animal" type="radio" value="both" /> Both
    [{{ $animal }}]

    {{--
        https://forum.laravel-livewire.com/t/unable-to-get-checkbox-value/157/7
    --}}

   <div class="mb-4">
        @foreach($options as $key=>$option)
        <div>
            <label class="inline-flex items-center">
                <input wire:model="options.{{ $key }}" name="options[{{ $key }}]" value="1" type="checkbox">
                <span class="ml-2">{{ $key }}</span>
            </label>
        </div>
        @endforeach
    </div>

    @foreach($options as $key=>$option)
        {{$key }}: {{($option) ? 'true' : 'false' }}<br />
    @endforeach



</div>
