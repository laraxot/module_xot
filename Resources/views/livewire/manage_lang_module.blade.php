<div>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Size</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($files as $file)
                <tr>
                    <td>{{ $file->getFilenameWithoutExtension() }}</td>
                    <td>{{ $file->getSize() }}</td>
                    <td><button wire:click="edit('{{ $file->getFilenameWithoutExtension() }}')"
                            class="btn btn-primary">Modifica</button></td>
                </tr>
            @endforeach
        <tbody>
    </table>

<<<<<<< HEAD
    <livewire:theme::modal.form_array data="test" bodyView="xot::livewire.manage_lang_module.edit" />
=======
    <livewire:modal.form_array data="test" bodyView="xot::livewire.manage_lang_module.edit" />
>>>>>>> 04f6c8ba (first)
</div>
