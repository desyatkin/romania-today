@if ($pagination['last_page'] > 1)
<center>
    <div class='pagination'>
        <ul>
            @if ($pagination['current_page'] > 3)
            <li><a href="{{ $url }}?page=1">1</a></li>&nbsp...&nbsp
            @endif
            @for ($i = ($pagination['current_page'] - 2); $i <= ($pagination['current_page']+2); $i++)
            @if ($i > 0 && $i <= $pagination['last_page'])
            <li @if($i == $pagination['current_page']) class="active" @endif>
                <a href="{{ $url }}?page={{ $i }}">{{$i}}</a>
            </li>
            @endif
            @endfor
            @if (($pagination['last_page'] - $pagination['current_page']) > 3)
            &nbsp...&nbsp<li><a href="{{ $url }}?page={{ $pagination['last_page'] }}">{{ $pagination['last_page'] }}</a></li>
            @endif
        </ul>
    </div>
</center>
@endif