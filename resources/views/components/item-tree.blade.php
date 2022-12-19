@foreach ($model as $key => $item)
<div class="itemable invisibled">
    @if (is_array($item))
            <div class="norootable clickable" data-path={{$path.$key}}><span>+</span>{{$key}}</div>
            @include('components.item-tree', ['model' => my_sort($item), 'path' => "$path$key/"])
    @else
        <div class="greenable openable" data-path={{$path.$item}}>{{$item}}</div>
    @endif
</div>
@endforeach
