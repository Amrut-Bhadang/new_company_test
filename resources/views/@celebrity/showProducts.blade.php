@if($products)
<div class="row">
 <div class="col-md-12">
 <div class="table-responsive">
    <table class="table table-striped table-bordered table-condensed" id="table" style="width: 100%;">
        <thead>
            <tr>
                <th>{{ __('Sr. no') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Category Name') }}</th>
                <th>{{ __('Kilo Points') }}</th>   
            </tr>
        </thead>
        <tbody>
            
            @foreach($products as $key => $product)
                <tr>
                    <td>{{$key + 1}}</td>
                    <td>{{$product->name}}</td>
                    <td>{{$product->products_type}}</td>
                    <td>{{$product->cat_name}}</td>
                    <td>{{$product->points}}</td>

                <tr>
                 
            @endforeach
                       
        </tbody>
    </table>
	</div>
  </div>      
</div>
@endif