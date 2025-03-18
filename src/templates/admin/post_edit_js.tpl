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
		  initialValue: {{{json_encode post.content}}}
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

		  fetch("/admin/posts/{{post.id}}", {
			method: "POST",
			body: formData,
		  })
			.then( function(response) {
			    //window.location.href = '/admin/posts';
			})
			.catch((error) => console.error(error));
		});
	};
</script>
