<?php $this->layout('admin::admin-layout', ['title' => 'Admin - Edit Post']) ?>
<?php 
	//var_dump($post); exit;
?>
<h2>Edit Post</h2>
<form action="/admin/posts/<?= $this->e($post->id)?>" id="postForm" method="POST">
  <?php if(isset($error)): ?>
    <div class="mb-3">
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $this->e($error)?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
  <?php endif; ?>
  <?php if(isset($result)): ?>
    <div class="mb-3">
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $this->e($result)?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
  <?php endif; ?>
  <div class="mb-3">
    <label for="title" class="form-label">Title</label>
    <input type="text" class="form-control" id="title" name="title" aria-describedby="titleHelp" value="<?= $this->e($post->title) ?>">
    <div id="titlelHelp" class="form-text">You cannot change the title</div>
  </div>
  <div class="mb-3">
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" id="publishedChecked" name="published" <?= $post->published ? 'checked' : ''?>>
      <label class="form-check-label" for="publishedChecked">Published</label>
    </div>
  </div>
  <div class="mb-3">
    <div>
		<label for="content" class="form-label">Content</label>
		<div id="content" name="content" class="form-control <?= isset($formErrors['content']) ? 'is-invalid' : ''?>" aria-describedby="contentHelp" required></div>
		<div id="contentlHelp" class="form-text">Your content must be at least 10 characters long</div>
		<div id="validationServerContent" class="invalid-feedback">
		  <?= $this->e($formErrors['content'] ?? '')?>
		</div>
    </div>
  </div>
  <button type="button" class="btn btn-secondary" id="cancel">Cancel</button>
  <button type="submit" class="btn btn-primary">Save</button>
</form>

<?php $this->push('js') ?>
<script type="text/javascript">
	window.onload = function() {
		var cancelButton = document.getElementById('cancel')
			cancelButton.addEventListener('click', function(e) {
			window.location.href = '/admin/posts'
		})

		const editor = new toastui.Editor({
		  el: document.querySelector('#content'),
		  height: '500px',
		  initialEditType: 'markdown',
		  previewStyle: 'vertical',
		  initialValue: '<?php echo $post->content; ?>'
		});

		editor.getMarkdown();
		
		var el = document.querySelector("#postForm #content .toastui-editor-pseudo-clipboard");

		
		const form = document.getElementById("postForm");
		form.addEventListener("submit", (e) => {
		  e.preventDefault();

		  var content = editor.getMarkdown();
		  const formData = new FormData(form);

		  formData.set('content', content); 
		  formData.set('published', formData.get('published') == 'on' ? 1 : 0); 
		  fetch("/admin/posts/<?= $this->e($post->id)?>", {
			method: "POST",
			body: formData,
		  })
			.then( function(response) {
				window.location.href = '/admin/posts';
			})
			.catch((error) => console.error(error));
		});
	};
</script>
<?php $this->end() ?>
