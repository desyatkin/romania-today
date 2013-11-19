@extends('site.main')

@section('content')


<div class="contentWrap contentWrapIndex">
    <div class="clear"></div>     
    <div class="block content">         
        <div class="theme_wrap">
            <div class="theme"><h2>Последние новости</h2></div>
        </div>
        @foreach($lastNews as $news)               
        <div class="newsBl">
            <img src="/{{ $news['preview'] }}" width="100" height="75" alt="">
            <h3><a href="{{ $news['url'] }}">{{ $news['article_name'] }}</a></h3>
            <span class="date_time_news">{{ $news['created_at'] }}</span>

            {{-- <span class="rubrika">Рубрика: <a href="/news/tourism/">Туризм</a></span> --}}

            {{ $news['content'] }}
            <div class="clear"></div>
        </div> <!-- /newsBl -->
        @endforeach


        <div class="block content">
            <div style="float:right; width:200px; "> 
            @include('helpers.sape')
            </div>
            <div style="clear:both;"></div>
        </div>



        <div style="clear:both;"></div><br>
        
        @include('banners.banner_bot')
             
        <div class="rss_news"><a href="/rss.php">Новости через rss</a></div>
        

    </div> <!-- /content -->
</div>



@endsection