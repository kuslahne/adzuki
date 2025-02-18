<script type="text/javascript">	
	var cancelButton = document.getElementById('cancel')
		cancelButton.addEventListener('click', function(e) {
		window.location.href = '/admin/categories'
	})

	const editor = new toastui.Editor({
	  el: document.querySelector('#description'),
	  height: '500px',
	  initialEditType: 'markdown',
	  previewStyle: 'vertical'
	});

	editor.getMarkdown();
	
	const form = document.getElementById("categoryForm");
	form.addEventListener("submit", (e) => {
	  e.preventDefault();
	  var content = editor.getMarkdown();
	  const formData = new FormData(form);
	  formData.set('description', content); 
	  fetch("/admin/categories", {
		method: "POST",
		body: formData,
	  })
	  .then( function(response) {
				window.location.href = '/admin/categories';
				console.log(response);
			}).catch((error) => console.error(error));
	});
</script>
