{{#>plain_layout}}
	{{#*inline "content"}}

	<main class="form-signin">
	  <form method="POST" action="{{login_url}}">
		<div class="logo"><img class="mb-4" src="/img/logo.png" alt="" width="100%"></div>
				{{#if flash}}
			<div class="message">
				{{{flash}}}
			</div>
		{{/if}}
		<p>User: <i>admin</i>, Password: <i>supersecret</i></p>
		<div class="form-floating">
			<label for="username">Username</label>
			<input type="text" class="form-control" id="username" name="username" placeholder="Username">
		</div>
		<div class="form-floating">
			<label for="password">Password</label>
			<input type="password" class="form-control" id="password" name="password" placeholder="Password">		  
		</div>

		<button class="w-100 btn btn-lg btn-primary" type="submit">Login</button>
		<p class="footer copyright">&copy; <?= date('Y')?> <a href="https://github.com/kuslahne/adzuki">Adzuki</a></p>
	  </form>
	</main>

	<link href="/css/login.css" rel="stylesheet">
	{{/inline}}
{{/plain_layout}}
