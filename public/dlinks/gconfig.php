<?php
/*
cConfigSm::instance('global_template1', '<a href="{url}">{img}<br />{text}</a>{comment}');
*/
cConfigSm::instance('global_template1', '<div class="b-sotmarket-banner mod_blue" style="width: 190px;"><div class="b-sotmarket-banner-head"></div><div class="b-sotmarket-banner-content"><a href="{url}" class="b-sotmarket-banner-product">
<img src="{img_url}" alt="" class="b-sotmarket-banner-product-image" /><span class="b-sotmarket-banner-product-text">{text}</span></a></div></div>');

cConfigSm::instance('global_template2', '<div class="b-sotmarket-banner mod_green"><div class="b-sotmarket-banner-head"></div><div class="b-sotmarket-banner-content"><div class="b-sotmarket-banner-product">
<a href="{url}" class="b-sotmarket-banner-product-text space_none">{text}</a></div></div></div>');

cConfigSm::instance('global_template3', '<div class="b-sotmarket-banner mod_blue" style="width: 200px; height:200px;"><div class="b-sotmarket-banner-head"></div><div class="b-sotmarket-banner-content" style="height:137px;">
<a href="{url}" class="b-sotmarket-banner-product"><img src="{img_url}" alt="" class="b-sotmarket-banner-product-image" />
<span class="b-sotmarket-banner-product-text">{text}</span></a></div></div>');

cConfigSm::instance('global_template4', '<div class="b-sotmarket-banner mod_pink" style="width: 190px;"><div class="b-sotmarket-banner-head"></div><div class="b-sotmarket-banner-content">
<div class="b-sotmarket-banner-product"><a href="{url}" class="b-sotmarket-banner-product-text space_none">{text}</a></div></div></div>');


cConfigSm::instance('global_show_img', 1);
cConfigSm::instance('global_utf8', '1'); // вывод в utf8
cConfigSm::instance('global_session_id', '/pages/informer/,/pages/feedback/,/pages/f/,/redirect/,/print/,/s/,/pages/informacija-dlja-reklamodatelej/,/news/economy/archive/,/news/politics/archive/,/news/tourism/archive/,/news/sport/archive/,/news/culture/archive/,/news/different/archive/');
cConfigSm::instance('global_db2_files', 10);
# технические параметры
cConfigSm::instance('global_default_anchor', 'http://www.sotmarket.ru/category/photo.html');
cConfigSm::instance('global_default_text', 'Все фотоаппараты в интернет-магазине Сотмаркет');
cConfigSm::instance('global_default_img', 'http://img.sotmarket.ru/des/new/b-nophoto_label.png');
cConfigSm::instance('global_url', 'http://www.sotmarket.ru/product/');
cConfigSm::instance('global_img_url', 'http://img.sotmarket.ru/resized/');
?>