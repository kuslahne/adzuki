<div class="main-column">	
	<h2>{{title}}</h2>
	{{> flash}}
	<form action="/admin/users{{#if user.id}}/{{user.id}}{{/if}}" method="POST">
	  <div class="mb-3">
		<label for="username" class="form-label">Username</label>
		<input type="text" class="form-control" id="username" name="username" aria-describedby="usernameHelp" value="{{user.username}}" {{#if editUser}}disabled{{/if}}>
		<div id="usernamelHelp" class="form-text">You cannot change the username</div>
	  </div>
	  <div class="mb-3">
		<div class="form-check form-switch">
		  <input class="form-check-input" type="checkbox" id="activeChecked" name="active" {{isChecked user.active}}>
		  <label class="form-check-label" for="activeChecked">Active</label>
		</div>
	  </div>
	  <div class="mb-3">
		<div class="accordion" id="accordionExample">
		  <div class="accordion-item">
			{{#if editUser}}
			<div class="hashLink">
				<a href="#hashLink"><button type="button">Change Password</button></a>
			</div>
			{{/if}}
			<div class="block-reveal">			  
				<div {{#if editUser}}id="hashLink"{{/if}}>
				<a href="#" class="close-btn">X</a>
						
				<div id="collapseOne" class="accordion-collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
				  <div class="accordion-body">
					<label for="password" class="form-label">Password</label>
					<input type="password" class="form-control {{#if formErrors.password}}is-invalid{{/if}}" id="password" name="password" aria-describedby="passwordHelp">
					<div id="passwordlHelp" class="form-text">Your new password must be at least 10 characters long</div>
					<div id="validationServerPassword" class="invalid-feedback">
					  {{formErrors.password}}
					</div>
					<label for="confirmPassword" class="form-label">Confirm Password</label>
					<input type="password" class="form-control {{#if formErrors.password}}is-invalid{{/if}}" id="confirmPassword" name="confirmPassword" aria-describedby="confirmHelp">
					<div id="confirmHelp" class="form-text">Re-enter the new password to confirm</div>
				  </div>
				</div>			  
			  </div>
			</div>

		  </div>
		</div>
	  </div>
	  <button type="button" class="btn btn-secondary" id="cancel">Cancel</button>
	  <button type="submit" class="btn btn-primary">Save</button>
	</form>
</div>


