<div id="header">
    <div id="header_wrap">
            
        <div id="header_left">
            <a href="/"><img alt="" src="/images/site/main/logo.png"></a>
        </div> <!-- /header_left -->
            
        <div id="header_info">
            @include ('helpers.weather_parse')
        </div> <!-- /header_info -->
            
        <div id="header_right">
        </div> <!-- /header_right -->
            
        <div class="clear"></div>
        

        <ul id="nav">
            @if ($categoryName == '') <li><span class="act">Главная</span></li>
            @else <li><a target="_self" href="/">Главная</a></li>
            @endif

            @if ($categoryName == 'Экономика') <li><span class="act">Экономика</span></li>
            @else <li><a target="_self" href="/news/economy/">Экономика</a></li>
            @endif

            @if ($categoryName == 'Политика') <li><span class="act">Политика</span></li>
            @else <li><a target="_self" href="/news/politics/">Политика</a></li>
            @endif
            
            @if ($categoryName == 'Культура') <li><span class="act">Культура</span></li>
            @else <li><a target="_self" href="/news/culture/">Культура</a></li>
            @endif
            
            @if ($categoryName == 'Туризм') <li><span class="act">Туризм</span></li>
            @else <li><a target="_self" href="/news/tourism/">Туризм</a></li>
            @endif
            
            @if ($categoryName == 'Спорт') <li><span class="act">Спорт</span></li>
            @else <li><a target="_self" href="/news/sport/">Спорт</a></li>
            @endif
            
            @if ($categoryName == 'Разное') <li><span class="act">Разное</span></li>
            @else <li><a target="_self" href="/news/different/">Разное</a></li>
            @endif
            
            @if ($categoryName == 'Информация о стране') <li><span class="act">Информация о стране</span></li>
            @else <li><a class="white" target="_self" href="/informacija-o-strane/obcshie-svedenija-o-rumynii/">Информация о стране</a></li>
            @endif
            
            @if ($categoryName == 'Туристу') <li><span class="act">Туристу
            @else <li><a class="last_li white" target="_self" href="/turistu/osobennosti-turizma-rumynii/">Туристу</a></li>
            @endif
            
        </ul>
    </div> <!-- /header_wrap -->
</div>
<!-- /header -->