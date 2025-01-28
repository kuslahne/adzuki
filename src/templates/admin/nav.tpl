
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light collapse">
<div class="position-sticky pt-3">
	<ul class="nav flex-column">
		<li class="nav-item">
			<a class="nav-link {{isactive blog.page 'users'}}" {{ariacurrent blog.page 'users'}} href="/admin/users">
			<span data-feather="home"></span>
			Users
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{isactive blog.page 'posts'}}" {{ariacurrent blog.page 'posts'}} href="/admin/posts">
			<span data-feather="posts"></span>
			Posts
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{isactive blog.page 'categories'}}" {{ariacurrent blog.page 'categories'}} href="/admin/categories">
			<span data-feather="categories"></span>
			Categories
			</a>
		</li>
	</ul>
</div>
</nav>
