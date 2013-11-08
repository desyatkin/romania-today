@extends('site.main')

@section('content')
<div class="contentWrap"> 
    <div class="block content">
        <div class="theme_wrap">
            <div class="theme"><h2>{{ $categoryName }}</h2></div>
        </div>
        @foreach($articles as $article)               
        <div class="newsBl">
            <h3><a href="{{ $url }}{{ $article['alias'] }}/">{{ $article['article_name'] }}</a></h3>
            <span class="date_time_news">{{ $article['created_at'] }}</span>

            <span class="rubrika">Рубрика: <a href="{{ $url }}">{{ $categoryName }}</a></span>

            <p>{{ strip_tags(mb_substr($article['meta_description'], 0, 300)) }} ...</p>
            <div class="clear"></div>
        </div> <!-- /newsBl -->
        @endforeach


        @include('site.pagination')
    </div>
</div>

@endsection