<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}



	/*
	|-------------------------------------------------------------------------------
	| Загрузка изображений
	|-------------------------------------------------------------------------------
	|
	| Функция загружает изображение, уменьшает до нужных размеров, создает
	| уменьшенную копию, заносит запись в таблицу изображений.
	|
	|-------------------------------------------------------------------------------
	*/
	protected function uploadImage ( $fieldName, $destinationPath ) {
		// Создаем объект
		$file = Input::file($fieldName);
		
		// проверяем есть ли изображение
		if( !is_object($file) ) return false;




		// Проверяем тип файла
		$extension       = $file->getClientOriginalExtension();
		if($extension != 'png' && $extension != 'jpg' && $extension != 'jpeg') {
			die('Не верный тип файла. Для загрузки доступны форматы PNG и JPEG');
		}


 
 		// Переносим в папку назначения с новым именем
		$filename        = str_random(30);
		$imagePath       = $destinationPath . $filename . '.' . $extension;
		$previewPath	 = $destinationPath . 'preview_' . $filename . '.' . $extension;
		$upload_success  = Image::make( Input::file($fieldName)->getRealPath() )
										->resize(1000, null, true)
										->save($imagePath);



		//  пробуем загрузить файл
		if($upload_success) {

			// Создаем уменьшенную копию
			$preview_success = Image::make($imagePath)
										->resize(200, null, true)
										->save($previewPath);
			if(!$preview_success) return false;

			return $previewPath;
		}
		else return false;	

	}


}