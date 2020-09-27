<div>

    <input wire:model="animal" name="animal" type="radio" value="cats" /> Cats
    <input wire:model="animal" name="animal" type="radio" value="dogs" /> Dogs
    <input wire:model="animal" name="animal" type="radio" value="both" /> Both
    [{{ $animal }}]
    <hr/>
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
    <hr/>


    @foreach($products as $product)
        <h3>{{ $product->title }}</h3>
        @foreach($change_cats as $change_cat)
            <h4>{{ $change_cat->title }}</h4>

            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-secondary">
                    <input type="radio"  name="changes[]"  autocomplete="off" value="-1"> -
                </label>
                <label class="btn btn-secondary active" >
                    <input type="radio"  name="changes[]"  autocomplete="off"  checked value="0"> &nbsp;
                </label>
                <label class="btn btn-secondary">
                    <input type="radio"  name="changes[]" autocomplete="off" value="1"> +
                </label>
            </div>
        @endforeach
    @endforeach
        <pre>
            {{ print_r($changes) }}
        </pre>

</div>
