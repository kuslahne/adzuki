<div class="main-column">
	<h2>Users</h2>
	{{> flash}}
	<form action="/admin/users" method="POST" style="float:right">
	<button type="submit" class="btn btn-primary"><i class="bi bi-person-plus"></i> Add user</button></form></h2>
	<table class="table table-hover">
	  <thead>
		<tr>
		  <th scope="col">#</th>
		  <th scope="col">Username</th>
		  <th scope="col">Active</th>
		  <th scope="col">Last login</th>
		  <th scope="col"></th>
		</tr>
	  </thead>
	  <tbody>
		<?php 
		  $i = $start;
		  $zero = strlen($total);
		?>

		{{#each users.all}}
			{{> admin_user_row}}
		{{/each}}

	  </tbody>
	</table>
	<div class="row">
	  <p>Total number of users: <strong>{{ users.total }}</strong></p>
	</div>
	
	{{> pagination}}
</div>

<!-- Modal -->
{{> admin_modal_delete}}

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
    xhr.open('DELETE', '/admin/users/' + e.target.attributes['data-id'].value)
    xhr.onload = function () {
      if (xhr.readyState == 4 && xhr.status == "200") {
        window.location.replace ('/admin/users');
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
