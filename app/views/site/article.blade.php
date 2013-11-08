@extends('site.main')

@section('content')
<div class="contentWrap">
            
    <div class="block content">
    @foreach ($article as $element)    
        <div class="newsBlTxt">
            <h1>{{ $element['article_name'] }}</h1>
            <span class="date_time_news">{{ $element['created_at'] }}</span> 
            <span class="rubrika">
                Рубрика: <a href="{{ $url }}">{{ $categoryName }}</a>
            </span>
            <span class="print"></span>
            <div class="clear"></div>
            <div class="newsImg"> 
                <img src="/{{ $element['preview'] }}" width="240" height="180" alt="">
                <p class="mainTitle">{{ $element['article_name'] }}</p>
            </div>
            <div class="ann">
                <p>{{ $element['description'] }}</p>
            </div>
            {{ $element['content'] }}

            <script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
            <div class="yashare-auto-init" data-yasharel10n="ru" data-yasharetype="none" data-yasharequickservices="yaru,vkontakte,facebook,twitter"></div>
            <div class="clear"></div>
        </div>
    @endforeach
    </div>

    <div class="block content">
        <div class="theme_wrap">
            <div class="theme"><h2>Новости по теме</h2></div>
        </div>              
        <div class="newsBl greyBG">
        @foreach($newsInCtaegory as $news)
            <div class="moreDate"><nobr>{{ $news['created_at'] }}</nobr></div>
            <div class="moreTxt">
                <a href="{{ $url }}{{ str_replace('-', '/', $news['created_at']) }}/{{ $news['alias'] }}/">
                    {{ $news['article_name'] }}
                </a>
            </div>
            <div class="clear"></div>
        @endforeach             
        </div>
        <div class="clear"></div>
    </div>      
</div>
@endsection