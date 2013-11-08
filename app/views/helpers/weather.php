<?
/*
|-------------------------------------------------------------------------------
| Вывод  погоды в Варшаве на текущий момент времени
| (блок в хедере справа от лого)
|-------------------------------------------------------------------------------
*/
file_put_contents(__DIR__.'/weather_parse.php', weather());

/*
|-------------------------------------------------------------------------------
| Функция парсит XML выдачу gismeteo.ru и формирует блок прогноза погоды
|-------------------------------------------------------------------------------
| Vars:
| $imgs(array)      - массив ссылок на иконки погодных явлений 
| $imgId[1]         - id элемента из массива imgs 
|                    (определяет иконку которая будет отображаться)
| $xml              - xml выдача гисметео
| $weatherArray[1]  - содержимое первого блока "FORECAST" из выдачи
| $temperature[1]   - температура max
| $temperature[2]   - температура min
|
| Выводит:
| $weatherBlock     - сформированый блок "Погода" для отображения на сайте
|
|-------------------------------------------------------------------------------
*/
function weather(){

    $imgs[0]    = "/images/site/main/sunly.png";
    $imgs[1]    = "/images/site/main/little_cloudy.png";
    $imgs[2]    = "/images/site/main/cloudy.png";
    $imgs[3]    = "/images/site/main/overcast.png";
    $imgs[4]    = "/images/site/main/rain.png";
    $imgs[5]    = "/images/site/main/big_rain.png";
    $imgs[6]    = "/images/site/main/snow.png";
    $imgs[7]    = "/images/site/main/snow.png";
    $imgs[8]    = "/images/site/main/thunderstorm.png";
    $imgs[9]    = "/images/site/main/none.png";
    $imgs[10]   = "/images/site/main/none.png";

    $imgId = '';

    $xml = file_get_contents('http://informer.gismeteo.ru/xml/76680_1.xml');
             

    // Забираем первый блок "FORECAST" из XML выдачи
    // он соттветствует прогнозу максимально приближеному к настоящему времени
    // (проще говоря прогноз на сейчас =) 
    preg_match("#<FORECAST .*?>(.*?)</FORECAST>#is", $xml, $weatherArray);

    // Забираем значение свойства "cloudiness" (оно одно на весь блок)
    // Определяет какая картинка бутет отображаться в блоке (солнышко, тучка, etc...)
    preg_match("#cloudiness=\"(.*?)\"#is", $weatherArray[1], $imgId);

    // Забираем свойства "max" и "min" тега "TEMPERATURE" 
    preg_match("#<TEMPERATURE max=\"(.*?)\" min=\"(.*?)\"/>#is", $weatherArray[1], $temperature);

    if($temperature[1] > 0) $temperature[1] = "+" .  $temperature[1];
    if($temperature[2] > 0) $temperature[2] = "+" .  $temperature[2];

    // Формируем блок
    $weatherBlock = '<span id="gradus">'  . $temperature[2] . '...' . $temperature[1] . ' °C </span>'
                  . '<div class="weather">' 
                  . '   <img id="weather1" alt="" src="' . $imgs[$imgId[1]] . '">'
                  . '</div>';
                 

    // Вывод
    return $weatherBlock;
}
