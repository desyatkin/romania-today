<?php

class ArticlesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex($categoryId = 999, $subcategoryId = 0)
	{
		// Получаем статьи с пагинацией
		if($categoryId == 999) $articles = Articles::paginate(25);
		else {
			if($subcategoryId != 0) {
				$articles = Articles::where('category_id', '=', $categoryId)
															->where('subcategory_id', '=', $subcategoryId)
															->paginate(25);
			}
			else $articles = Articles::where('category_id', '=', $categoryId)->paginate(25);
		}

		// Получаем список категорий
		$categories = Categories::where('parent_id', '=', 0)->get();

		// Получаем список подкатегорий
		$subcategories = Categories::where('parent_id', '=', $categoryId)->get();

		$view = View::make('admin.articles.list')
						->with('articles', $articles)
						->with('categories', $categories)
						->with('subcategories', $subcategories)
						->with('categoryId', $categoryId)
						->with('subcategoryId', $subcategoryId);

		return $view;
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreate()
	{
		// получаем список категорий
		$categories = Categories::where('parent_id', '=', 0)
									->get();

		// Биндим переменные для редаткирования
		$article['id'] = '';
		$article['category_id'] = '';
		$article['subcategory_id'] = '';
		$article['article_name'] = '';
		$article['alias'] = '';
		$article['header'] = '';
		$article['meta_title'] = '';
		$article['meta_description'] = '';
		$article['description'] = '';
		$article['content'] = '';
		$article['preview'] = '';

		$view = View::make('admin.articles.create')
						->with('article', $article)
						->with('categories', $categories)
						->with('subcategories', array());

		return $view;
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function postStore()
	{
		if( !Input::has('articleName') ) die('Поле название обязательно для заполнения');

		// Загружаем изображение 
		$preview = $this->uploadImage('preview', 'userfiles/');

		//  Если редактирование, то выбираем элемент
		if(Input::has('id')) $article = Articles::find( Input::get('id') );
		else $article                   = new Articles;

		$article->category_id      = Input::get('category');
		$article->subcategory_id   = Input::get('subcategory');
		$article->article_name     = Input::get('articleName');
		$article->alias            = Input::get('alias');
		$article->header           = Input::get('header');
		$article->meta_title       = Input::get('meta_title');
		$article->meta_description = Input::get('meta_description');
		$article->description      = Input::get('description');
		$article->content          = Input::get('content');

		// Заносим в базу только если есть изображение
		if($preview) $article->preview = $preview;
		
		$article->save();

		return Redirect::to('/admin/articles/');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getEdit($id)
	{
		// получаем список категорий
		$categories = Categories::where('parent_id', '=', 0)
									->get();

		// Получаем статью
		$article = Articles::find($id)->toArray();

		// Получаем список подкатегорий
		$subcategories = Categories::where('parent_id', '=', $article['category_id'])->get()->toArray();


		// debug($subcategories); exit;

		$view = View::make('admin.articles.create')
						->with('article', $article)
						->with('categories', $categories)
						->with('subcategories', $subcategories);

		return $view;
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getDestroy($id)
	{
		$article = Articles::find($id)->delete();

		return Redirect::to('/admin/articles/');
	}

	//------------------------------------------------------------------------------
	// Отдает список подкатегорий
	//------------------------------------------------------------------------------
	public function postSubcategories() {
		if(!Input::has('parentId')) return false;
		$result = '';

		$subcategories = Categories::where('parent_id', '=', Input::get('parentId'))
										->get()
										->toArray();


		foreach($subcategories as $subcategory) {
			$result .= '<option value="'. $subcategory['id'] .'">'. $subcategory['category_name'] .'</option>';
		}

		return $result;
	}

}