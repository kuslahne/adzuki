<dialog id="favDialog">
  <form method="dialog">
	<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="confirmModalLabel">Confirm delete</h5>
		  </div>
		  <div class="modal-body">
			Are you sure to delete {{repo}} `<span id="dataModal"></span>`?<br />
			Please note, this action cannot be undone.
		  </div>
		  <div class="modal-footer">
			<div id="deleteError"></div>
			<button>Close dialog</button>
			<button type="button" class="btn btn-danger" id="confirmDelete" data-id="">Confirm</button>
		  </div>
		</div>
	  </div>
	</div>    
  </form>
</dialog>

<script type="text/javascript"> 
  var elDelete = document.querySelectorAll(".del-row"); 
  const favDialog = document.getElementById('favDialog');

  elDelete.forEach(function(elem) {
	elem.addEventListener("click", function(e) {
		favDialog.showModal();
		document.getElementById('deleteError').style.visibility = "hidden"
		document.getElementById('confirmDelete').disabled = false
		document.getElementById('dataModal').innerHTML = e.target.attributes['data-modal'].value;
		document.getElementById('confirmDelete').setAttribute('data-id', e.target.attributes['data-id'].value)
	});
  });

  var confirmDelete = document.getElementById('confirmDelete');
  confirmDelete.addEventListener('click', function(e) {
    var xhr = new XMLHttpRequest()
    xhr.open('DELETE', '/admin/{{page}}/' + e.target.attributes['data-id'].value)
    xhr.onload = function () {
      if (xhr.readyState == 4 && xhr.status == "200") {
        location.reload()
      } else {
        var response = JSON.parse(xhr.responseText)
        document.getElementById('confirmDelete').disabled = true
        document.getElementById('deleteError').innerHTML = response.error
        document.getElementById('deleteError').style.visibility = "visible"
      }
    }
    xhr.send()
  })

</script>
