@extends('admin.layout')

@section('content')


<div class="pull-left">
	<h2>Категории</h2>
</div>

<div class="pull-right">
	<a href="/admin/categories/create/{{ $parentId }}" class="btn btn-success">
		<span class="glyphicon glyphicon-plus"></span>
		Добавить категорию
	</a>
</div>

<div style="clear: both;"></div>


{{-- Хлебные крошки --}}
@if($parentCategory)
<ol class="breadcrumb">
  <li><a href="/admin/categories/">Категории</a></li>    
  <li class="active">{{ $parentCategory->category_name }}</li>
</ol>
@endif


{{-- Выводим список категорий --}}
@foreach($categories as $category)

	{{-- Кнопка для удаления категории --}}
	<span class="glyphicon glyphicon-trash" title="Удалить" onclick="deleteCategory({{ $category->id }});"></span>
	
	{{-- В категории можно войти, в подкатегории нет --}}
	@if($category->parent_id == 0)
		<a href="/admin/categories/show/{{ $category->id }}">{{ $category->category_name }}</a>		
	@else
		{{ $category->category_name }}
	@endif

	<br>

@endforeach

<script type="text/javascript">
	
	//------------------------------------------------------------------------------
	// Удаление категории
	//------------------------------------------------------------------------------
	function deleteCategory (id) {
		if(confirm('Действительно удалить?')) {
			document.location = '/admin/categories/destroy/' + id;
		}
		else return false;
	}

</script>


@endsection