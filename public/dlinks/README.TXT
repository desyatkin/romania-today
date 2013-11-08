1. Папка data должна быть доступна для записи. Права 666.
2. Настройки в gconfig.php

# Необходимо настроить:

Шаблоны для вывода:
cConfigSm::instance('global_template1', '<a href="{url}"><img src="{img_url}" alt="" /><br />{text}</a><br />{comment}');
cConfigSm::instance('global_template2', '<a href="{url}"><img src="{img_url}" alt="" /><br />{text}</a><br />{comment}');
cConfigSm::instance('global_template3', '<a href="{url}"><img src="{img_url}" alt="" /><br />{text}</a><br />{comment}');
Можно создать неограниченное количество вариантов шаблонов

Возможные параметры в шаблоне: {img},{img_url},{url},{text},{comment}
{img} - подставляется <img src="{img_url">, если включено в глобальных настройках. Удобно если есть необходимость скрывать изображение. Если у товара нет изображения, выведется пустая строка
{img_url} - url с изображением товара, если включено в глобальных настройках. Если у товара нет изображения (на практике очень маловероятно), будет выводиться заглушка из cConfigSm::instance('global_default_img')
{url} - url товара
{text} - текст ссылки
{comment} - html комментарий в случае ошибки вывода

cConfigSm::instance('global_show_img', 1);
Поставить 1, если будут использоваться изображения. Если 0, то в {img_url} будет выводиться cConfigSm::instance('global_default_img'), а в {img} пустая строка.
После смены параметра необходимо запустить scron.php, чтобы перестроить базу.

cConfigSm::instance('global_session_id', '/search=,sid=,/admin/,ref=,utm_');
Идентификатор сессии или частей в URL, для которых не будет запоминаться индивидуальная ссылка. На таких страницах покажется общая-универсальная (см. ниже). Можно указывать несколько значений через запятую. Регистрозависим, т.е. utm_ и UTM_ не одно и то же.
Если нужно определять запятую в URL, экранируйте её: \,
Можно оставить пустое.

cConfigSm::instance('global_db2_files', 10);
Для малого количества уникальных страниц достаточно 1. На рабочем скрипте менять нельзя, только предварительно!
Оптимально 1 для 50к страниц сайта.

//cConfigSm::instance('global_utf8', '1'); вывод в utf8
Если нужен вывод на сайте в utf, откомментируйте эту строчку

# Технические параметры, менять нельзя:

cConfigSm::instance('global_default_anchor', 'http://www.sotmarket.ru/category/photo.html');
URL универсальной ссылки, она же покажется в случае ошибки вывода индивидуальной.

cConfigSm::instance('global_default_text', 'Все фотоаппараты в интернет-магазине Сотмаркет');
Текст универсальной ссылки.

cConfigSm::instance('global_url', 'http://www.sotmarket.ru/product/');
Маска URL к страницам товара Сотмаркета.

cConfigSm::instance('global_img_url', 'http://img.sotmarket.ru/resized/');
Маска URL к картинкам Сотмаркета.

cConfigSm::instance('global_default_img', 'http://www.sotmarket.ru/d/i/b-nophoto_label.png');
url картинки-заглушки в случае отсутствия реального изображения товара.

3. Перед первым запускам надо запустить scron.php
Файл scron.php нужно поставить в крон и запускать его раз в два часа.

В случае смены global_show_img, также необходимо запустить scron.php
Он оптимизирует поиск в db2. Обновляет ключевики. Пересчитывает статистику.

4. Встраивание на странице.

require_once('slibs/cKeysDb.php');
// Где $sUrl  урл страницы для которой отображается блок. Например $_SERVER['REQUEST_URI']
// В функции вызова вторым параметром указываем номер шаблона. 
echo cKeysDb::getBlock($sUrl,2);