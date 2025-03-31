@if($records)
<div class="form-group">
	<label for="category">SubCategory TreeView</label>
	@foreach($records as $category)
		<div class="tree-item active has-children">
			<!-- <i class="expand-icon"></i><i class="fa fa-folder"></i> -->
			<span class="radio">
				<input type="radio" id="{{ $category->id }}" {{$category->id == $sub_category_id ? "checked" : ""}} name="parent_id" value="{{ $category->id }}">
				<label for="{{ $category->id }}" class=""></label>
				<span for="{{ $category->id }}">{{ $category->name }}</span>
			</span>

            @if(count($category->childs))
                @include('subcategory/manageChildNew',['childs' => $category->childs])
            @endif
		</div>
    @endforeach

</div>
@endif