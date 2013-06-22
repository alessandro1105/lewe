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
				</div>
				<nav id="nav">
					<ul>
						<li id="menu_homepage"><a href="<?php echo Config::_PAGE_SITE_INDEX; ?>">Home</a></li>
                        <li id="menu_how-it-works"><a href="/?<?php echo Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_HOWITWORKS; ?>">Scopri come funziona</a></li>
                        <li id="menu_code"><a href="/?<?php echo Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_CODE; ?>">Open Source Project</a></li>
                        <!--<li id="menu_contact"><a href="/?<?php echo Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_CONTACT; ?>">Contattaci</a></li>-->
						<li id="menu_login"><a href="/?<?php echo Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_LOGIN; ?>">Login</a></li>
					</ul>
				</nav>
			</div>
		</div>
	</header>
</div>