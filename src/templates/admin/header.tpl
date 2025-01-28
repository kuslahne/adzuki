    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#"><img src="/img/logo.png" width="100"></a>
    <div class="navbar-nav">
        <div class="nav-item text-nowrap" style="color:white">
            <div class="avatar"><span class="bi--people-circle"></span>
			  {{ session.username }} 
			</div> 	
			
            <a class="no-underline px-3" href="{{ session.link_logout }}">Logout</a>
        </div>
    </div>
    </header>
