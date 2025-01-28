<dialog id="favDialog">
  <form method="dialog">
	<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="confirmModalLabel">Confirm delete</h5>
		  </div>
		  <div class="modal-body">
			Are you sure to delete post `<span id="dataModal"></span>`?<br />
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
