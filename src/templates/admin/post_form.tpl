<div class="main-column">	
	<h2>{{title}}</h2>
	{{> flash}}
	<form action="/admin/posts/{{post.id}}" id="postForm" method="POST">
	  {{#if formErrors}}
		<div class="mb-3">
		  <div class="alert alert-danger alert-dismissible fade show" role="alert">
			{{formErrors.content}}
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
			<label for="title" class="form-label">Title</label>
			<input type="text" class="form-control" id="title" name="title" aria-describedby="titleHelp" value="{{post.title}}">
			<div id="titleHelp" class="form-text">You cannot change the title</div>
		  </div>
		  <div>
			<label for="slug" class="form-label">Slug <span class="small">(optional)</span></label>
			<input type="text" class="form-control" id="slug" name="slug" aria-describedby="slugHelp" value="{{post.slug}}">
			<div id="slugHelp" class="form-text">You cannot change the slug</div>
		  </div>

	  </div>
	  <div class="mb-3">
		<div class="form-check form-switch">
		  <input class="form-check-input" type="checkbox" id="publishedChecked" name="published" {{isChecked post.published}}>
		  <label class="form-check-label" for="publishedChecked">Published</label>
		</div>
	  </div>
	  <div class="mb-3">
			<label for="content" class="form-label">Content</label>
			<div id="content" name="content" class="form-control {{#if formErrors.content}}is-invalid{{/if}}" aria-describedby="contentHelp" required></div>
			<div id="contentHelp" class="form-text">Your content must be at least 10 characters long</div>
			<div id="validationServerContent" class="invalid-feedback">
			  {{formErrors.content}}
			</div>
	  </div>
	  <button type="button" class="btn btn-secondary" id="cancel">Cancel</button>
	  <button type="submit" class="btn btn-primary">Save</button>
	</form>
</div>

