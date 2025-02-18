<div class="main-column">	
	<h2>{{title}}</h2>
	{{> flash}}
	<form action="/admin/{{page}}/{{item.id}}" id="categoryForm" method="POST">
	  {{#if formErrors}}
		<div class="mb-3">
		  <div class="alert alert-danger alert-dismissible fade show" role="alert">
			{{formErrors.description}}
			<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
		  </div>
		</div>
	  {{/if}}
	  {{#if result}}
		<div class="mb-3">
		  <div class="alert alert-success alert-dismissible fade show" role="alert">
			<?= $this->e($result)?>
			<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
		  </div>
		</div>
	  {{/if}}

	  <div class="mb-flex">
		  <div>
			<label for="title" class="form-label">Name</label>
			<input type="text" class="form-control" id="name" name="name" aria-describedby="nameHelp" value="{{item.name}}">
			<div id="titleHelp" class="form-text">You cannot change the title</div>
		  </div>
		  <div>
			<label for="meta_description" class="form-label">Meta Description<span class="small"> (optional)</span></label>
		    <textarea id="meta" name="meta_description" class="form-control" aria-describedby="metaHelp">{{item.meta_description}}</textarea>
		  </div>

	  </div>
	  <div class="mb-3">
			<label for="description" class="form-label">Description</label>
			<div id="description" name="description" class="form-control {{#if formErrors.description}}is-invalid{{/if}}" aria-describedby="contentHelp" required></div>
			<div id="descriptionHelp" class="form-text">Your description must be at least 10 characters long</div>
			<div id="validationServerDescription" class="invalid-feedback">
			  {{formErrors.description}}
			</div>
	  </div>
	  <button type="button" class="btn btn-secondary" id="cancel">Cancel</button>
	  <button type="submit" class="btn btn-primary">Save</button>
	</form>
</div>

