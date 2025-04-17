<x-col size="12">
    <h3>{{ count($files) }} Error Logs </h3>
    <ol>
        @foreach ($files as $file)
            <li>
                <a href="?_act=artisan&cmd=error-show&log={{ $file->getFilename() }}">{{ $file->getFilename() }}</a>
            </li>
        @endforeach
    </ol>
    <h3> Ulrs</h3>
    <ol>
        @foreach ($urls as $url)
            <li>
                <a href="{{ $url }}">{{ $url }}</a>
            </li>
        @endforeach
    </ol>
    <pre>
    {!! $content !!}
    </pre>
</x-col>
