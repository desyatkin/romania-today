<?php 

Class Articles extends Eloquent {

	protected $table = 'articles';

	//------------------------------------------------------------------------------
	// Категории статей
	//------------------------------------------------------------------------------
	public function category()
	{
		return $this->belongsTo('Categories', 'category_id');

	}

	//------------------------------------------------------------------------------
	// Подкатегории
	//------------------------------------------------------------------------------
	public function subcategory()
	{
		return $this->belongsTo('Categories', 'subcategory_id');
	}

}