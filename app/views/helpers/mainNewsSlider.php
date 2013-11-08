<?php
/* Этот helper собирает html для бока-слайдера "Главные Новости" на главной странице.
Вынос данного действия в отдельный модуль обусловлен необходимостью написать полноценный php скрипт
который позволит собрать блок в одном цикле */

// собираем в переменные элементы слайдера
$slider = '';
$mainBlock = '';
foreach ($randomArticles as $key => $article) {
    $slider .= '<li class="ui-tabs-nav-item ';
    if ($key == 0) $slider .= 'ui-tabs-selected';
    $slider .= '" id="nav-fragment-' . ($key+1) . '">'
             . '<a href="#fragment-' . ($key+1) . '">'
             . '<span>' . $article['article_name'] . '</span>'
             . '</a>'
             . '</li>';

    $mainBlock .= '<div id="fragment-' . ($key+1) .'" class="ui-tabs-panel';
    if ($key != 0) $mainBlock .= ' ui-tabs-hide';
    $mainBlock .= '" style="">'
                . '<img src="/' . $article['preview'] . '" width="320" height="240" alt="" />'
                . '<div class="info">'
                . '<p><a href="' . $article['url'] . '">' . $article['article_name'] . '</a></p>'
                . '</div>'
                . '</div>';
}

// выводим
echo '<div id="featured" class="ui-tabs ui-widget ui-widget-content ui-corner-all">';
echo '<ul class="ui-tabs-nav">' . $slider . '</ul>' . $mainBlock; 
echo '</div>';
