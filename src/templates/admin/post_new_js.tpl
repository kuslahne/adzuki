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
	  .then( function(response) {
				//window.location.href = '/admin/posts';
			}).catch((error) => console.error(error));
	});
</script>
