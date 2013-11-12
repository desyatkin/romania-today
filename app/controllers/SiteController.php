<?php

/*
|-------------------------------------------------------------------------------
| Контроллер управлющий выводом контента на сайт
|-------------------------------------------------------------------------------
| 
|-------------------------------------------------------------------------------
|
|-------------------------------------------------------------------------------
*/
class SiteController extends \BaseController {

    //------------------------------------------------------------------------------
    // Основные методы
    //------------------------------------------------------------------------------
    /*
    |-------------------------------------------------------------------------------
    | Показывает главную страницу сайта
    |-------------------------------------------------------------------------------
    | 
    |-------------------------------------------------------------------------------
    */
    public function getShowIndex() {
        
        // Получаем переменные необходимые для отображения
        // блока-слайдера и сайдбара
        // 3 статьи для блока-слайдера
        $randomArticles         = $this->blockArticles(3);
        // 6 случайных статей из категории новости для блоков в сайдбаре
        $sidebarRandomArticles  = $this->blockArticles(6);
        // 6 статей (по одной из каждой подкатегории катетогрии "новости")
        $previewBlocks          = $this -> getPreviewBlocks();
        
        // имя категории (в данной функции только определяет активную закладку в меню
        //                  без нее шаблон не соберется)
        $categoryName = '';

        // 8 последних статей для блока контента
        $lastNews = $this->blockArticles(8, 'id DESC');
        // отправляем все переменные во view
        $view = View::make('site.index')
                    ->with('lastNews'               , $lastNews)
                    ->with('sidebarRandomArticles'  , $sidebarRandomArticles)
                    ->with('randomArticles'         , $randomArticles)
                    ->with('categoryName'           , $categoryName)
                    ->with('previewBlocks'          , $previewBlocks);

        // Возвращаем сформированную страницу
        return $view;
    }

    /*
    |-------------------------------------------------------------------------------
    | Показывает категорию
    |-------------------------------------------------------------------------------
    | 
    |-------------------------------------------------------------------------------
    */
    public function getShowCategory($category, $subcategory = false) {

        // Проверяем не являтся ли данная категория исключением,
        // (обрабатывется другой функцией и имеет другой шаблон)
        if ( $category == 'informacija-o-strane' ||
             $category == 'turistu') {
            return $this->getShowException($category, $subcategory);
        }

        // Пытаемся получить id и имя категории по её псевдониму
        $categoryArray = Categories::select('id', 'category_name')
                                   ->where('alias', '=', $category)
                                   ->where('parent_id', '=', 0)
                                   ->get()
                                   ->toArray();
        // Если категории нет -> 404
        if (empty($categoryArray)) 
            $this->error404();
        
        // Если категория существует переменным $categoryId и $categoryName
        // присваюваются соответствующие элементы массива
        $categoryId     = $categoryArray[0]['id'];
        $categoryName   = $categoryArray[0]['category_name'];

        // если указана подкатегория
        if ($subcategory) {
            // Пытаемся получить id и имя категории по её псевдониму и id категории 
            $subcategoryArray = Categories::select('id', 'category_name')
                                          ->where('alias', '=', $subcategory)
                                          ->where('parent_id', '=', $categoryId)
                                          ->get()
                                          ->toArray();
            // если подкатегории нет -> 404
            if (empty($subcategoryArray)) {
                $this->error404();
            }
            

            $subcategoryId = $subcategoryArray[0]['id'];
            $categoryName  = $subcategoryArray[0]['category_name'];
        } else {
            $subcategoryId = 0;
        }
        
        // Пытаемся получить статьи по id категории и id подкатегории
        // (полученый массив будет так же содержать данные для построения пагинации)
        $articlesAndPagination = Articles::where('category_id', '=', $categoryId)
                                         ->where('subcategory_id', '=', $subcategoryId)
                                         ->orderBy('id', 'DESC')
                                         ->paginate(12)
                                         ->toArray();

        // Если статей нет -> 404
        if (empty($articlesAndPagination['data'])) 
            $this->error404();

        // забираем из массива элемент со статьями (он последний)
        // оставшиеся элементы(данные для пагинации) для удобства восприятия 
        // отправляем в массив $pagination
        $articles               = array_pop($articlesAndPagination);
        $pagination             = $articlesAndPagination;

        // Получаем переменные необходимые для отображения
        // блока-слайдера и сайдбара
        // 3 статьи для блока-слайдера
        $randomArticles         = $this->blockArticles(3);
        // 6 случайных статей из категории новости для блоков в сайдбаре
        $sidebarRandomArticles  = $this->blockArticles(6);
        // 6 статей (по одной из каждой подкатегории катетогрии "новости")
        $previewBlocks          = $this -> getPreviewBlocks();

        // собираем link который будет префиксом для всех ссылок на статьи в данной категории
        $url                    = '/' . $category . '/';
        if ($subcategory) $url .= $subcategory . '/';

        // отправляем все переменные во view
        $view = View::make('site.category')
                    ->with('sidebarRandomArticles'  , $sidebarRandomArticles)
                    ->with('randomArticles'         , $randomArticles)
                    ->with('previewBlocks'          , $previewBlocks)
                    ->with('articles'               , $articles)
                    ->with('pagination'             , $pagination)
                    ->with('categoryName'           , $categoryName)
                    ->with('url'                    , $url);

        // возвращаем сформированную страницу
        return $view;
    }


    /*
    |-------------------------------------------------------------------------------
    | Показывает статью
    |-------------------------------------------------------------------------------
    | 
    |-------------------------------------------------------------------------------
    */
    public function getShowArticle($category, $subcategory, $year = 0, $month = 0, $day = 0, $alias) {
        // ишем в базе корневую категорию по её псевдониму (alias)
        $categoryArray = Categories::select('id', 'category_name')
                                   ->where('alias', '=', $category)
                                   ->where('parent_id', '=', 0)
                                   ->get()
                                   ->toArray();
        // если категории нет -> 404
        if (empty($categoryArray)) 
            $this->error404();

        $categoryId   = $categoryArray[0]['id'];
        $categoryName = $categoryArray[0]['category_name'];

        // если указана подкатегория (subcategory != false)
        if ($subcategory) {
            // пытаемся получить категорию с псевдонином $subcategory 
            // id родителя равным id корневой категории
            $subcategoryArray = Categories::select('id', 'category_name')
                                          ->where('alias', '=', $subcategory)
                                          ->where('parent_id', '=', $categoryId)
                                          ->get()
                                          ->toArray();
            
            // если такой категории не существует, считаем что пользователь сошел с ума
            // и выдаем 404
            if (empty($subcategoryArray)) 
                $this->error404;

            // если категория существует переменной subcategoryId присваивается значение id подкатегории
            // и переменная categoryName меняется на имя подкатегории
            $subcategoryId = $subcategoryArray[0]['id'];
            $categoryName  = $subcategoryArray[0]['category_name'];
        } else {
            // если подкатегория не указана subcategoryId присваивается 0
            $subcategoryId = 0;
        }

        // ищем статью из данной категории/подкатегории
        $article = Articles::where('alias', '=', $alias)
                           ->where('category_id', '=', $categoryId)
                           ->where('subcategory_id', '=', $subcategoryId)
                           ->orderBy('id', 'DESC')
                           ->limit(1)
                           ->get()
                           ->toArray();

        // если статьи нет -> 404
        if (empty($article)) 
            $this->error404();

        
        // проверка даты
        // данные из ссылки на страницу должны совпадать с датой создания этой страницы
        // если не совпадают -> 404
        $date = $year . '-' . $month . '-' . $day;
        if (strval($article[0]['created_at']) != strval($date))
            $this->error404();

        // берем 2 случайных статьи для блоков в сайдбаре
        $saidbarArticles        = $this->blockArticles(2);
        // берем 5 случайных статьи из текущей катигории
        $newsInCtaegory         = $this->articleFromCategory($categoryId, $subcategoryId, 5);
        $randomArticles         = $this->blockArticles(3);
        $sidebarRandomArticles  = $this->blockArticles(6);

        // перебираем массив категории 
        // и для каждого элемента берем по 2 последних статьи.
        // Формируем новый массив previewBlocks содержащий псевдонимы категорий(alias)
        // и выбранные из них статьи
        $previewBlocks          = $this -> getPreviewBlocks();

        // собираем ссылку на категорию в которой находится статья
        $url                    = '/' . $category . '/';
        if ($subcategory) $url .= $subcategory . '/';

        // отправляем все переменные во view
        $view = View::make('site.article')
                    ->with('sidebarRandomArticles'  , $sidebarRandomArticles)
                    ->with('randomArticles'         , $randomArticles)
                    ->with('previewBlocks'          , $previewBlocks)
                    ->with('categoryName'           , $categoryName)
                    ->with('url'                    , $url)
                    ->with('article'                , $article)
                    ->with('newsInCtaegory'         , $newsInCtaegory);

        // возвращаем сформированную страницу
        return $view;
    }

    /*
    |-------------------------------------------------------------------------------
    | Показывает категорию-исключение или статью-исключение
    |-------------------------------------------------------------------------------
    | (вывод категории или статьи не попадающей под общий шаблон обработки)
    |-------------------------------------------------------------------------------
    */
    public function getShowException($categoryAlias, $articleAlias)
    {
        // ишем в базе корневую категорию по её псевдониму (alias)
        $categoryArray = Categories::select('id', 'category_name')
                                   ->where('alias', '=', $categoryAlias)
                                   ->where('parent_id', '=', 0)
                                   ->get()
                                   ->toArray();

        // если категории нет -> 404
        if (empty($categoryArray)) 
            $this->error404();
        
        $categoryId     = $categoryArray[0]['id'];
        $categoryName   = $categoryArray[0]['category_name'];

        $randomArticles = $this->blockArticles(3);
        $sidebarRandomArticles = $this->blockArticles(6);
        $previewBlocks = $this -> getPreviewBlocks();
        $url = '/' . $categoryAlias . '/';

        if (!$articleAlias) {
            $articlesAndPagination = Articles::where('category_id', '=', $categoryId)
                                             ->where('subcategory_id', '=', 0)
                                             ->orderBy('id', 'DESC')
                                             ->paginate(12)
                                             ->toArray();

            if (empty($articlesAndPagination['data'])) 
                $this->error404();


            $articles               = array_pop($articlesAndPagination);
            $pagination             = $articlesAndPagination;

            

            $view = View::make('site.category_exception')
                        ->with('sidebarRandomArticles'  , $sidebarRandomArticles)
                        ->with('randomArticles'         , $randomArticles)
                        ->with('previewBlocks'          , $previewBlocks)
                        ->with('categoryName'           , $categoryName)
                        ->with('url'                    , $url)
                        ->with('rightMenuLinks'         , $linksForMenu)
                        ->with('pagination'             , $pagination)
                        ->with('articles'               , $articles);

        }else{
            // ищем статью из данной категории/подкатегории
            $article = Articles::where('alias', '=', $articleAlias)
                               ->where('category_id', '=', $categoryId)
                               ->where('subcategory_id', '=', 0)
                               ->orderBy('id', 'DESC')
                               ->limit(1)
                               ->get()
                               ->toArray();

            // если статьи нет -> 404
            if (empty($article)) 
                $this->error404();

            $view = View::make('site.article_exception')
                        ->with('sidebarRandomArticles'  , $sidebarRandomArticles)
                        ->with('randomArticles'         , $randomArticles)
                        ->with('previewBlocks'          , $previewBlocks)
                        ->with('categoryName'           , $categoryName)
                        ->with('url'                    , $url)
                        ->with('article'                , $article);

        }
        return $view;
    }




    //------------------------------------------------------------------------------
    // Вспомогательные методы
    //------------------------------------------------------------------------------
    /*
    
    /*
    |-------------------------------------------------------------------------------
    | Ошибка 404 not found
    |-------------------------------------------------------------------------------
    */
    public function error404(){
        echo '<center><div style="font-size:70px; margin: 10% 0;">404<br> Not Found</div></center>';
        die();
    }

    /*
    |-------------------------------------------------------------------------------
    | Получить по одной случайной статье для каждой подкатегории категории "новости"
    |-------------------------------------------------------------------------------
    */
    public function getPreviewBlocks()
    {
        // Получаем массив всех подкатегорий категории news
        $otherCategories = Categories::select('id', 'alias', 'category_name')
                                     ->where('parent_id', '=', 1)
                                     ->get()
                                     ->toArray();
        $i = 0;
        $previewBlocks = array();
        // перебираем массив категории 
        // и для каждого элемента берем по 2 последних статьи.
        // Формируем новый массив previewBlocks содержащий псевдонимы категорий(alias)
        // и выбранные из них статьи
        foreach ($otherCategories as $category) {
            $previewArticles = Articles::whereRaw('subcategory_id = ' . $category['id'] . ' order by RAND()')
                                       ->limit(1)
                                       ->get()
                                       ->toArray();

            $previewBlocks[$i]['category_alias'] = $category['alias'];
            $previewBlocks[$i]['category_name']  = $category['category_name'];
            $previewBlocks[$i]['articles']       = $previewArticles;
            $i++;
        }

        return $previewBlocks;
    }
    /*
    |-------------------------------------------------------------------------------
    | Выбрать случайные или последние статьи
    |-------------------------------------------------------------------------------
    | 
    |-------------------------------------------------------------------------------
    */
    public function blockArticles($limit, $order = 'RAND()'){
        // Берем несколько случайных или последних статей
        //(количество статей определяется переменной limit)
        $articles =  Articles::select('id', 'category_id', 'subcategory_id', 'alias', 'article_name', 'content', 'preview', 'created_at')
                             ->whereRaw('category_id = 1 order by ' . $order)
                             ->limit($limit)
                             ->get()
                             ->toArray();

        // Изменяем-дополняем полученый массив
        foreach ($articles as &$element) {
            // Собираем ссылку на статью и добавляем её в массив
            // Добавляем к ссылке alias категории
            $url =  '/' . implode(Categories::select('alias')->find($element['category_id'])->toArray()) . '/';
            
            // Если статья находится не в корневой категории то добавляем alias подкатегории к ссылке
            $subcategory_alias = Categories::select('alias')->find($element['subcategory_id']);
            if (!is_null($subcategory_alias)) $url .= implode($subcategory_alias->toArray()) . '/';
            
            // *!*!*!*!*!*!*!*!*!*!*!*!*!*!*!*!*!*!*!*!*!*!
            // ссылка на конкретную страницу должна содержать в себе дату её создания
            $url .= str_replace('-', '/', $element['created_at']) . '/';
            // *!*!*!*!*!*!*!*!*!*!*!*!*!*!*!*!*!*!*!*!*!*!

            // Добавляем к ссылке alias статьи          
            $url .= $element['alias'];

            // Изменяем исходный массив (добавляем элемент с ключем "url")
            $element['url'] = $url;
            
            // Обрезаем текст статьи для анонса
            $element['content'] = mb_substr($element['content'], 0, 110) . '...';

            // Обрезаем слишком длинные заголовки 
            //(заголовки должны умещаться в одну строку иначе ломается верстка)
            if (mb_strlen($element['article_name']) > 55)
                $element['article_name'] = mb_substr($element['article_name'], 0, 55) . '...';
        }

        // возвращаем обработанный массив статей
        return $articles;
    }

    /*
    |-------------------------------------------------------------------------------
    | Выбрать последние статьи из категории
    |-------------------------------------------------------------------------------
    | 
    |-------------------------------------------------------------------------------
    */
    public function articleFromCategory($categoryId, $subcategoryId, $limit) {
        $articles = Articles::whereRaw('category_id = ' . $categoryId . ' AND subcategory_id = ' . $subcategoryId . ' order by RAND()')
                           ->limit($limit)
                           ->get()
                           ->toArray();

        return $articles;
    }

    /*
    |-------------------------------------------------------------------------------
    | Generate article url from id
    |-------------------------------------------------------------------------------
    */
    public function getArticleURL ($id) {

        //get article
        $article = Articles::find($id);

        // compose url
        $url = 'http://polishnews.ru/' . $article->category->alias;
        if( isset($article->subcategory->alias) ) $url .= '/'. $article->subcategory->alias;
        $url .= '/'. $article->alias;

        return $url;

    }

    /*
    |-------------------------------------------------------------------------------
    | RSS для яндекс новостей
    |-------------------------------------------------------------------------------
    */
    public function getRSS() {

        header("Content-Type:   application/rss+xml");


        // description protocol, open xml document
        echo '<?xml version="1.0" encoding="utf-8"?>' . "\n"
        . '<rss version="2.0" xmlns="http://backend.userland.com/rss2" xmlns:yandex="http://news.yandex.ru">' . "\n"
        . '<channel>' . "\n"
        . '<title>Румыния сегодня</title>' . "\n"
        . '<link>http://romania-today.ru/</link>' . "\n"
        . '<description>Актуальные новости и интересные статьи.</description>' . "\n"
        . '<image>' . "\n"
        . '<url>http://romania-today.ru/images/site/main/logo.gif</url>' . "\n"
        . '<title>Румыния сегодня</title>' . "\n"
        . '<link>http://romania-today.ru/</link>' . "\n"
        . '</image>' . "\n";


        // get 20 last articles
        $articles = Articles::orderBy('created_at', 'desc')->limit(20)->get();

        foreach($articles as $article) {

            echo '<item>' . "\n";
            echo '  <title>'. $article->article_name .'</title>' . "\n";
            echo '  <link>'. $this->getArticleURL($article->id) .'</link> . "\n"';
            echo '  <category>Разное</category>' . "\n";
            echo '  <enclosure url="http://romania-today.ru/userfiles/'. $article->preview .'" type="image/jpeg"/>' . "\n";
            echo '  <pubDate>'. date( 'r', strtotime($article->created_at) ) .'</pubDate>' . "\n";
            echo '  <yandex:genre>message</yandex:genre>' . "\n";
            echo '  <yandex:full-text>'. htmlspecialchars(strip_tags($article->description)) .'</yandex:full-text>' . "\n";
            echo '</item>' . "\n";
        }

        // close xml document
        echo '</channel>
              </rss>';
    }


    /*
    |-------------------------------------------------------------------------------
    | function generate sitemap
    |-------------------------------------------------------------------------------
    */
    public function getSitemap() {

        header("Content-Type:   application/xml");

        // open xml document
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation=" http://www.sitemaps.org/schemas/sitemap/0.9">';

        $articles = Articles::all();

        foreach($articles as $article) {
            echo '<url>' . "\n";

            echo '<loc>'. $this->getArticleURL($article->id) .'</loc>' . "\n";
            echo '<priority>0.5</priority>' . "\n";
            echo '<lastmod>'. $article->updated_at .'</lastmod>' . "\n";

            echo '</url>' . "\n";
        }

        //close xml document
        echo '</urlset>';
    }



    /*
    |-------------------------------------------------------------------------------
    | function make redirect to other url
    |-------------------------------------------------------------------------------
    */
    public function getRedirects() {
        return Redirect::to( Input::get('url') );
    }

    
    /*
    |-------------------------------------------------------------------------------
    | Функция разового использования. 
    | Удаляет слеш в начале ссылки на превью картинки
    | для использования - разкоментировать и добавить соответствующий роут
    |-------------------------------------------------------------------------------
    */
    
    public function getDeleteSlashes()
    {
    	$articles= Articles::all();
    	foreach ($articles as $article) {
    		if(mb_substr($article->preview, 0, 1) == '/'){
    			$article->preview = mb_substr($article->preview, 1);
    			echo $article->preview . "<br> \n";
    			$article->save();
    		}
    	}
    
    }
}