@extends('site.main')

@section('content')
<div class="contentWrap">
    <div class="block content">
        <div id="txtContent">
            @foreach ($article as $element)
            {{ $element['content'] }}
            @endforeach
        </div>
    </div>
</div>
@endsection