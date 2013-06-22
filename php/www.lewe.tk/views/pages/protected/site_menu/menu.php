<?php

	use \Config\Config;
	
?>


<!-- Menu -->

<div id="header-wrapper">
	<header class="container" id="site-header">
		<div class="row">
			<div class="12u">
                
                <div id="logo">
                	<img src="/images/logo.png" />
                    <h1>di <?php echo $objUser->Username; ?></h1>
				</div>
                
				<nav id="nav">
					<ul>
						<!--<li id="menu_home"><a href="/?<?php echo Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_HOME; ?>">Home</a></li>-->
						<li id="menu_my_lewe"><a href="/?<?php echo Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_MY_LEWE; ?>">Il mio Lewe</a></li>
						<li id="menu_public_lewe"><a href="/?<?php echo Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_PUBLIC_LEWE; ?>">Pubblico il mio Lewe</a></li>
						<li id="menu_friend_lewe"><a href="/?<?php echo Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_FRIEND_LEWE; ?>">Lewe di un amico</a></li>
						<li id="menu_logout"><a href="/?<?php echo Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_LOGOUT; ?>">Logout</a></li>
					</ul>
				</nav>
			</div>
		</div>
	</header>
</div>