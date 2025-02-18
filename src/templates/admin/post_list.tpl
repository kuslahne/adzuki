<div class="main-column">	
	<h2>Posts</h2>
	{{> flash}}
	 <form action="/admin/posts" method="POST" style="float:right">
	<button type="submit" class="btn btn-primary"><i class="bi bi-person-plus"></i> Add post</button></form></h2>
	<table class="table table-hover">
	  <thead>
		<tr>
		  <th scope="col">#</th>
		  <th scope="col">Title</th>
		  <th scope="col">Published</th>
		  <th scope="col"></th>
		</tr>
	  </thead>
	  <tbody>
		<?php 
		  $i = $start;
		  $zero = strlen($total);
		?>

		{{#each blog.posts}}
			{{> admin_post_row}}
		{{/each}}

	  </tbody>
	</table>
	<div class="row">
	  <p>Total number of posts: <strong>{{ blog.total }}</strong></p>
	</div>
	
	{{> pagination}}
</div>

<!-- Modal -->
{{> admin_modal_delete}}


