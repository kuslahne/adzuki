<?php $this->layout('admin::admin-layout', ['title' => 'Admin - New Post']) ?>

<h2>New Post</h2>
<form action="/admin/posts" id="postForm" method="POST">
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
    <input type="text" class="form-control <?= isset($formErrors['title']) ? 'is-invalid' : ''?>" id="title" name="title" aria-describedby="titleHelp" value="<?= $this->e($title ?? '') ?>" required>
    <div id="titleHelp" class="form-text">A title is use for title tag.</div>
    <div id="validationServerTitle" class="invalid-feedback">
      <?= $this->e($formErrors['title'] ?? '')?>
    </div> 
  </div>
  <div class="mb-3">
    <label for="content" class="form-label">Content</label>
         <div id="editor"></div>
    <div id="content" name="content" class="form-control <?= isset($formErrors['content']) ? 'is-invalid' : ''?>" aria-describedby="contentHelp" required></div>
    <div id="contentHelp" class="form-text">Your content must be at least 100 characters long</div>
    <div id="validationServerContent" class="invalid-feedback">
      <?= $this->e($formErrors['content'] ?? '')?>
    </div>
  </div>
  <button type="button" class="btn btn-secondary" id="cancel">Cancel</button>
  <button type="submit" class="btn btn-primary">Save</button>
</form>

<?php $this->push('js') ?>
<script type="text/javascript">	
	var cancelButton = document.getElementById('cancel')
		cancelButton.addEventListener('click', function(e) {
		window.location.href = '/admin/posts'
	})

	const editor = new toastui.Editor({
	  el: document.querySelector('#content'),
	  height: '500px',
	  initialEditType: 'markdown',
	  previewStyle: 'vertical'
	});

	editor.getMarkdown();
	
	const form = document.getElementById("postForm");
	form.addEventListener("submit", (e) => {
	  e.preventDefault();
	  var content = editor.getMarkdown();
	  const formData = new FormData(form);
	  formData.set('content', content); 
	  fetch("/admin/posts", {
		method: "POST",
		body: formData,
	  })
		.then((response) => console.log(response))
		.catch((error) => console.error(error));
	});
</script>
<?php $this->end() ?>
