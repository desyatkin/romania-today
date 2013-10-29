@extends('admin.layout')

@section('content')

{{-- Подключаем TinyMCE --}}
<script type="text/javascript" src="/tinymce/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "#content",    
 });
</script>

<h2>Добавить статью</h2>

<form class="form-horizontal" role="form" action="/admin/articles/store" method="POST" onsubmit="checkForm(); return false;" enctype="multipart/form-data">

	{{-- Название --}}
	<div class="form-group">
		<label for="articleName" class="col-lg-2 control-label">Название</label>
		<div class="col-lg-10">
			<input type="text" class="form-control" id="articleName" name="articleName" placeholder="Название статьи" onkeyup="alias_article();" value="{{ $article['article_name'] }}">
		</div>
	</div>


	{{-- ЧПУ --}}
	<div class="form-group">
		<label for="alias" class="col-lg-2 control-label">ЧПУ</label>
		<div class="col-lg-10">
			<input type="text" class="form-control" id="alias" name="alias" placeholder="ЧПУ" value="{{ $article['alias'] }}">
		</div>
	</div>


	{{-- Категория --}}
	<div class="form-group">
		<label for="category" class="col-lg-2 control-label">Категория</label>
		<div class="col-lg-10">		
			<select name="category" id="category" class="form-control" onchange="getSubcategories();">
				<option value="0"></option>
				@foreach($categories as $category)
					<option value="{{ $category->id }}" @if($category['id'] == $article['category_id']) selected @endif>
						{{ $category->category_name }}
					</option>
				@endforeach
			</select>
		</div>
	</div>


	{{-- Подкатегория --}}
	<div class="form-group">
		<label for="subcategory" class="col-lg-2 control-label">Подкатегория</label>
		<div class="col-lg-10">		
			<select name="subcategory" id="subcategory" class="form-control" @if(empty($subcategories)) disabled @endif>
				<option></option>
				@foreach($subcategories as $subcategory)
					<option value="{{ $subcategory['id'] }}" @if($subcategory['id'] == $article['subcategory_id']) selected @endif>
						{{ $subcategory['category_name'] }}
					</option>
				@endforeach
			</select>
		</div>
	</div>


	{{-- H1 заголовок --}}
	<div class="form-group">
		<label for="header" class="col-lg-2 control-label">H1 заголовок</label>
		<div class="col-lg-10">
			<input type="text" class="form-control" id="header" name="header" placeholder="H1 заголовок" value="{{ $article['header'] }}">
		</div>
	</div>


	{{-- meta title --}}
	<div class="form-group">
		<label for="meta_title" class="col-lg-2 control-label">Meta title</label>
		<div class="col-lg-10">
			<input type="text" class="form-control" id="meta_title" name="meta_title" placeholder="Meta title" value="{{ $article['meta_title'] }}">
		</div>
	</div>


	{{-- meta description --}}
	<div class="form-group">
		<label for="meta_description" class="col-lg-2 control-label">Meta description</label>
		<div class="col-lg-10">
			<input type="text" class="form-control" id="meta_description" name="meta_description" placeholder="Meta description" value="{{ $article['meta_description'] }}">
		</div>
	</div>


	{{-- description --}}
	<div class="form-group">
		<label for="description" class="col-lg-2 control-label">Описание</label>
		<div class="col-lg-10">
			<textarea class="form-control" id="description" name="description" placeholder="Описание">{{ $article['description'] }}</textarea>
		</div>
	</div>


	{{-- content --}}
	<div class="form-group">
		<label for="content" class="col-lg-2 control-label">Контент</label>
		<div class="col-lg-10">
			<textarea class="form-control" id="content" name="content" placeholder="Контент">{{ $article['content'] }}</textarea>
		</div>
	</div>


	{{-- preview --}}
	<div class="form-group">
		<label for="preview" class="col-lg-2 control-label">Изображение</label>
		<div class="col-lg-10">
			{{-- Выводим фото --}}
			@if ( !empty($article['preview']) ) 
				<img src="/{{ $article['preview'] }}">
			@endif

			<input type="file" class="form-control" id="preview" name="preview">
		</div>
	</div>

	{{-- Скрытое поле с id --}}
	<input type="hidden" name="id" value="{{ $article['id'] }}">


	{{-- Кнопки управления --}}
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
			<button type="submit" onclick="javascript:history.go(-1)" class="btn btn-default">Отмена</button>
			<button type="submit" class="btn btn-primary">Сохранить</button>
		</div>
	</div>




</form>

<script type="text/javascript">
	
	//------------------------------------------------------------------------------
	// Получает список подкатегорий AJAX'ом в зависимости от выбранной категории
	//------------------------------------------------------------------------------
	function getSubcategories() {
		var parentId = $('#category').val();

		$.post('/admin/articles/subcategories', { parentId: parentId }, function (data) {
			$('#subcategory').find('option').remove().end()
			$('#subcategory').append(data);

			$('#subcategory').prop( "disabled", false );

		});
	}


	//------------------------------------------------------------------------------
	// Проверяем заполнение обязательных полей
	//------------------------------------------------------------------------------
	function checkForm() {
		var articleName = $('#articleName').val();
		var category    = $('#category').val();

		if(articleName == '' || category == '' || category == 0){
			alert('Поля Название и Категория обязательны для заполнения');
		}
		else submit();
	}


	//------------------------------------------------------------------------------
	// Транслитерация
	//------------------------------------------------------------------------------
	function alias_article() {
		var name = $('#articleName').val();
		alias = cyr2lat(name);
		$('#alias').val(alias);
	}

	function cyr2lat(str) {

	    var cyr2latChars = new Array(
	['а', 'a'], ['б', 'b'], ['в', 'v'], ['г', 'g'],
	['д', 'd'],  ['е', 'e'], ['ё', 'yo'], ['ж', 'zh'], ['з', 'z'],
	['и', 'i'], ['й', 'y'], ['к', 'k'], ['л', 'l'],
	['м', 'm'],  ['н', 'n'], ['о', 'o'], ['п', 'p'],  ['р', 'r'],
	['с', 's'], ['т', 't'], ['у', 'u'], ['ф', 'f'],
	['х', 'h'],  ['ц', 'c'], ['ч', 'ch'],['ш', 'sh'], ['щ', 'shch'],
	['ъ', ''],  ['ы', 'y'], ['ь', ''],  ['э', 'e'], ['ю', 'yu'], ['я', 'ya'],

	['А', 'A'], ['Б', 'B'],  ['В', 'V'], ['Г', 'G'],
	['Д', 'D'], ['Е', 'E'], ['Ё', 'YO'],  ['Ж', 'ZH'], ['З', 'Z'],
	['И', 'I'], ['Й', 'Y'],  ['К', 'K'], ['Л', 'L'],
	['М', 'M'], ['Н', 'N'], ['О', 'O'],  ['П', 'P'],  ['Р', 'R'],
	['С', 'S'], ['Т', 'T'],  ['У', 'U'], ['Ф', 'F'],
	['Х', 'H'], ['Ц', 'C'], ['Ч', 'CH'], ['Ш', 'SH'], ['Щ', 'SHCH'],
	['Ъ', ''],  ['Ы', 'Y'],
	['Ь', ''],
	['Э', 'E'],
	['Ю', 'YU'],
	['Я', 'YA'],

	['a', 'a'], ['b', 'b'], ['c', 'c'], ['d', 'd'], ['e', 'e'],
	['f', 'f'], ['g', 'g'], ['h', 'h'], ['i', 'i'], ['j', 'j'],
	['k', 'k'], ['l', 'l'], ['m', 'm'], ['n', 'n'], ['o', 'o'],
	['p', 'p'], ['q', 'q'], ['r', 'r'], ['s', 's'], ['t', 't'],
	['u', 'u'], ['v', 'v'], ['w', 'w'], ['x', 'x'], ['y', 'y'],
	['z', 'z'],

	['A', 'A'], ['B', 'B'], ['C', 'C'], ['D', 'D'],['E', 'E'],
	['F', 'F'],['G', 'G'],['H', 'H'],['I', 'I'],['J', 'J'],['K', 'K'],
	['L', 'L'], ['M', 'M'], ['N', 'N'], ['O', 'O'],['P', 'P'],
	['Q', 'Q'],['R', 'R'],['S', 'S'],['T', 'T'],['U', 'U'],['V', 'V'],
	['W', 'W'], ['X', 'X'], ['Y', 'Y'], ['Z', 'Z'],

	[' ', '-'],['0', '0'],['1', '1'],['2', '2'],['3', '3'],
	['4', '4'],['5', '5'],['6', '6'],['7', '7'],['8', '8'],['9', '9'],
	['-', '-']

	    );

	    var newStr = new String();

	    for (var i = 0; i < str.length; i++) {

	        ch = str.charAt(i);
	        var newCh = '';

	        for (var j = 0; j < cyr2latChars.length; j++) {
	            if (ch == cyr2latChars[j][0]) {
	                newCh = cyr2latChars[j][1];

	            }
	        }
	        // Если найдено совпадение, то добавляется соответствие, если нет - пустая строка
	        newStr += newCh;

	    }
	    // Удаляем повторяющие знаки - Именно на них заменяются пробелы.
	    // Так же удаляем символы перевода строки, но это наверное уже лишнее
	    return newStr.replace(/[-]{2,}/gim, '-').replace(/\n/gim, '');
	}

</script>

@endsection