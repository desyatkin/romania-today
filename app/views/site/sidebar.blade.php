<div class="rightBl">

    @foreach($sidebarRandomArticles as $key => $article)

    @if($key == 0)
    <div class="block info_bl topRight ">
    @else
    <div class="block info_bl @if($key%2 != 0) firstBl @endif">
    @endif
        <div class="bl_pre">
            <div class="bl_catTitle"></div>
            <img src="/{{ $article['preview'] }}" alt="{{ $article['article_name'] }}" width="200" height="180">
            <span><a href="{{ $article['url'] }}">{{ $article['article_name'] }}</a></span>
            
        </div>          
        <div class="readMore"><a href="{{ $article['url'] }}">подробнее</a></div>
        <div class="date_time">{{ $article['created_at'] }}</div>
    </div>

    @endforeach

    @foreach($previewBlocks as $key => $block)
    <div class="block info_bl  @if($key%2 != 0) firstBl @endif">
        @foreach($block['articles'] as $article)
        <div class="bl_pre">
            <div class="bl_catTitle"><a href="/news/{{ $block['category_alias'] }}/">{{ $block['category_name'] }}</a></div>
            <img src="/{{ $article['preview'] }}" alt="{{ $block['category_name'] }}"  width="200" height="180">
            <span><a href="/news/{{ $block['category_alias'] }}/{{ str_replace('-', '/', $article['created_at']) }}/{{ $article['alias'] }}/">
                    {{ $article['article_name'] }}</a>
            </span>
        </div>          
        <div class="readMore">
            <a href="/news/{{ $block['category_alias'] }}/{{ str_replace('-', '/', $article['created_at']) }}/{{ $article['alias'] }}/">подробнее</a>
        </div>
        <div class="date_time">{{ $article['created_at'] }}</div>
        @endforeach
    </div>
    @endforeach

    <div class="block info_bl fixed-height">
        @include('banners.banner_sidebar')
    </div>

    <div class="block info_bl firstBl fixed-height">
        @include('helpers.sotmarket')
    </div>

    <div class="block info_bl fixed-height">
        <p>Реклама:</p>
        @include('helpers.sape')
    </div>

    <div class="informers">
        <div class="block dop_bl">
            <div class="dopInfo_bl">
                <h4>Румыния Сегодня в&nbsp;удобном формате:</h4>
                <ul>
                    <li class="rss"><a href="/rss.php">RSS лента</a></li>
                    <li class="twitter"><a href="http://twitter.com/romania_today">Twitter</a></li>
                    <li class="yandex"><a href="http://www.yandex.ru/?add=59064&amp;from=promocode">Виджет на Яндексе</a></li>
                </ul>
            </div>
        </div> <!-- /block dop_bl -->

        <div class="block dop_bl">
            <a id="ourInformer" href="/pages/informer/">Поставьте наш информер</a>
        </div> <!-- /block dop_bl -->

        <div class="block dop_bl">
            <div class="yaform-holster"><div class="yandexform yaform yaform_hint" onclick="return {type: 3, logo: 'rb', arrow: false, webopt: false, websearch: false, bg: '#E4E4E4', fg: '#000000', fontsize: 12, suggest: true, site_suggest: true, encoding: '', language: 'ru'}" id="yandexform0" style="display: block;"><form class="yaform__form" method="get" action="http://yandex.ru/sitesearch"><table class="yaform__search" cellpadding="0" cellspacing="0"><tbody><tr><td class="yaform__search-input"><table class="yaform__search-input-layout"><tbody><tr><td class="yaform__search-input-layout-l"><div class="yaform__input"><input name="text" value="" class="yaform__input-text yaform__input-text_hint" autocomplete="off"><div class="b-suggest" onclick="return {url: 'http://sitesuggest.yandex.ru/suggest-ya.cgi?v=2&amp;callback=?', adjust: 'true'}"><div class="b-suggest-holster" style="display: none;"><div class="b-suggest-popup"><div class="b-suggest-list"><iframe frameborder="0" src="javascript:'<body style=\'background:none;overflow:hidden\'>'"></iframe><ul class="b-suggest-items"></ul></div></div></div></div></div></td><td class="yaform__search-input-layout-r"><input type="hidden" name="searchid" value="1467678"><input type="hidden" name="l10n" value="ru"><input type="hidden" name="web" value="0"><input class="yaform__submit yaform__submit_image" type="submit" value=""></td></tr></tbody></table></td></tr><tr><td class="yaform__gap"><div class="yaform__gap-i"></div></td></tr></tbody></table></form></div></div><script type="text/javascript" src="http://site.yandex.net/load/form/1/form.js" charset="utf-8"></script>
        </div> <!-- /block dop_bl -->

    </div> <!-- /informers -->


    <noindex>  
        <div class="block info_bl banner_bl firstBl">
            <a rel="nofollow" href="http://store.sony.ru/"><img src="/images/site/main/sony1.gif"></a>
        </div>
        <div class="block info_bl banner_bl">
            <a rel="nofollow" href="http://www.sony.ru/article/id/1237488920256"><img src="/images/site/main/sony2.jpg"></a>
        </div>
    </noindex>

</div>

<div class="clear"></div><br>