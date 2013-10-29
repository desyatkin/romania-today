@extends('admin.layout')

@section('content')

{{-- Подключаем TinyMCE --}}
<script type="text/javascript" src="/tinymce/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea",    
 });
</script>

<h2>Добавить категорию</h2>

<form class="form-horizontal" role="form" action="/admin/categories/store" method="POST" onsubmit="checkForm(); return false;">
	
	{{-- Название категории --}}
	<div class="form-group">
		<label for="categoryName" class="col-lg-2 control-label">Название категории</label>
		<div class="col-lg-10">
			<input type="text" class="form-control" id="categoryName" name="categoryName" placeholder="Название категории" onkeyup="alias_category();">
		</div>
	</div>


	{{-- ЧПУ --}}
	<div class="form-group">
		<label for="alias" class="col-lg-2 control-label">ЧПУ</label>
		<div class="col-lg-10">
			<input type="text" class="form-control" id="alias" name="alias" placeholder="ЧПУ">
		</div>
	</div>


	{{-- H1 заголовок --}}
	<div class="form-group">
		<label for="header" class="col-lg-2 control-label">H1 заголовок</label>
		<div class="col-lg-10">
			<input type="text" class="form-control" id="header" name="header" placeholder="H1 заголовок">
		</div>
	</div>


	{{-- Meta title --}}
	<div class="form-group">
		<label for="metaTitle" class="col-lg-2 control-label">Meta title</label>
		<div class="col-lg-10">
			<input type="text" class="form-control" id="metaTitle" name="metaTitle" placeholder="Meta title">
		</div>
	</div>


	{{-- Meta description --}}
	<div class="form-group">
		<label for="metaDescription" class="col-lg-2 control-label">Meta description</label>
		<div class="col-lg-10">
			<input type="text" class="form-control" id="metaDescription" name="metaDescription" placeholder="Meta description">
		</div>
	</div>


	{{-- description --}}
	<div class="form-group">
		<label for="description" class="col-lg-2 control-label">Описание</label>
		<div class="col-lg-10">
			<input type="text" class="form-control" id="description" name="description" placeholder="Описание">
		</div>
	</div>


	{{-- content --}}
	<div class="form-group">
		<label for="content" class="col-lg-2 control-label">Контент</label>
		<div class="col-lg-10">
			<textarea class="form-control" id="content" name="content" placeholder="Контент"></textarea>
		</div>
	</div>

	{{-- id родительской категории --}}
	<input type="hidden" name="parentId" value="{{ $parentId }}">


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
// Проверяем заполнение обязательных полей
//------------------------------------------------------------------------------
function checkForm() {	
	var categoryName = $('#categoryName').val();	
	if(categoryName != '') submit();
	else {
		alert('Поле имя категории обязательно для заполнения');
		return false;	
	} 
}


//------------------------------------------------------------------------------
// Транслитерация
//------------------------------------------------------------------------------
function alias_category() {
	var name = $('#categoryName').val();
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