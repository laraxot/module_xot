<x-dashboard-tile :position="$position" :refresh-interval="$refreshIntervalInSeconds">
    <div class="font-bold text-center">{!! $articleTitle !!}</div>
    <div class="text-sm font-bold text-center articletile">{!! $articleContent !!}</div>
</x-dashboard-tile>
@push('styles')
<style>
.articletile img{
    width:220px;

}
</style>
@endpush
