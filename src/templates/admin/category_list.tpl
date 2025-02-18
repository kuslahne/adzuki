<div class="main-column">	
	<h2>Categories</h2>
	{{> flash}}
	 <form action="/admin/categories" method="POST" style="float:right">
	<button type="submit" class="btn btn-primary"><i class="bi bi-person-plus"></i> Add category</button></form></h2>
	<table class="table table-hover">
	  <thead>
		<tr>
		  <th scope="col">#</th>
		  <th scope="col">Name</th>
		  <th scope="col">Description</th>
		  <th scope="col"></th>
		</tr>
	  </thead>
	  <tbody>
		<?php 
		  $i = $start;
		  $zero = strlen($total);
		?>

		{{#each categories}}
			{{> admin_category_row}}
		{{/each}}

	  </tbody>
	</table>
	<div class="row">
	  <p>Total number of categories: <strong>{{ total }}</strong></p>
	</div>
	
	{{> pagination}}
</div>

<!-- Modal -->
{{> admin_modal_delete}}


