<!-- seleziono la voce di menu -->
<script>

	function onLoadFunction() {
		$("#menu_login").addClass("current_page_item");
	}
	
	window.onload = onLoadFunction;
</script>

		<!-- Main -->
<?php

	use \Config\Config;
	
?>

<div id="main-wrapper">
	<div class="container">
   		
        <div id="loginFormContainer">
        	
            <p id="loginFormTitle">Accesso al Lewe personale</p>
        
            <form name="loginForm" action="/?<?php echo Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_LOGIN; ?>" method="post">
                <p class="loginFormText">E-mail </p><input class="loginFormInput" name="<?php echo Config::_HTTP_REQUEST_USER_EMAIL; ?>" type="text" value="" placeholder="E-mail" onkeydown="if(event.keyCode==13) document.forms['loginForm'].submit();" /><br />
                <p class="loginFormText">Password </p><input class="loginFormInput" name="<?php echo Config::_HTTP_REQUEST_USER_PASSWORD; ?>" type="password" value="" placeholder="Password" onkeydown="if(event.keyCode==13) document.forms['loginForm'].submit();" /><br />
                <input id="loginFormCookie" name="<?php echo Config::_HTTP_REQUEST_USER_COOKIE; ?>" type="checkbox" value="" /><p class="loginFormText">Ricordami </p><br />
                <div id="loginFormButtonContainer">
                	<a class="button" id="loginFormSubmit" onclick="document.forms['loginForm'].submit();">Login</a>
                </div>
            </form>
            <a class="loginFormText" id="loginFormRegistration" href="/?<?php echo Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_REGISTRATION; ?>">Non sei ancora registrato?</a>
        
            <?php
            
                if ($blErrorLogin) {
            ?>
            <p id="loginFormErrorText"><?php echo "E-mail o password errati"; ?></p>
            <?php
                }
            ?>
    	</div>
	</div>
</div>
