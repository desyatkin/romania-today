<?php

class CategoriesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		// получаем список корневых категорий
		$rootCategories = Categories::where('parent_id', '=', '0')->get();

		// формируем вид
		$view = View::make('admin.categories.list')
						->with('parentId', '')
						->with('parentCategory', '')
						->with('categories', $rootCategories);

		return $view;
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreate($parentId = null)
	{
		$view = View::make('admin.categories.create')
						->with('parentId', $parentId);

		return $view;
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function postStore()
	{
		if(!Input::has('categoryName')) return 'Поле Имя обязательно для заполнения';

		$category                   = new Categories;
		$category->parent_id        = Input::get('parentId');
		$category->category_name    = Input::get('categoryName');
		$category->alias            = Input::get('alias');
		$category->header           = Input::get('header');
		$category->meta_title       = Input::get('metaTitle');
		$category->meta_description = Input::get('metaDescription');
		$category->description      = Input::get('description');
		$category->content          = Input::get('content');
		$category->save();

		return Redirect::to('/admin/categories');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getShow($parentId)
	{
		// получаем список подкатегорий
		$rootCategories = Categories::where('parent_id', '=', $parentId)->get();

		// Получаем родительскую категорию для хлебных крошек
		$parentCategory = Categories::find($parentId);

		// формируем вид
		$view = View::make('admin.categories.list')		
						->with('parentId', $parentId)
						->with('parentCategory', $parentCategory)
						->with('categories', $rootCategories);

		return $view;
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
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
		$category = Categories::find($id)->delete();

		return Redirect::to('/admin/categories');
	}

}