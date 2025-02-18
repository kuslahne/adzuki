<script type="text/javascript">
	window.onload = function() {
		var cancelButton = document.getElementById('cancel')
			cancelButton.addEventListener('click', function(e) {
			window.location.href = '/admin/categories'
		})		

		const editor = new toastui.Editor({
		  el: document.querySelector('#description'),
		  height: '500px',
		  initialEditType: 'markdown',
		  previewStyle: 'vertical',
		  initialValue: {{{json_encode item.description}}}
		});

		editor.getMarkdown();
		console.log({{post.published}});
		
		var el = document.querySelector("#categoryForm #descripition .toastui-editor-pseudo-clipboard");

		
		const form = document.getElementById("categoryForm");
		form.addEventListener("submit", (e) => {
		  e.preventDefault();

		  var content = editor.getMarkdown();
		  const formData = new FormData(form);
		  
		  
          console.log(formData);
		  formData.set('description', content); 
		  fetch("/admin/categories/{{item.id}}", {
			method: "POST",
			body: formData,
		  })
			.then( function(response) {
				console.log(formData);
				window.location.href = '/admin/categories';
			})
			.catch((error) => console.error(error));
		});
	};
</script>
