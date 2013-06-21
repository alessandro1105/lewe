<?php
	
	use \Config\Config;
	
	$strEmail = isset($this->objRequest->_POST[Config::_HTTP_REQUEST_USER_EMAIL]) ? $this->objRequest->_POST[Config::_HTTP_REQUEST_USER_EMAIL] : "";
	
	$strEmailConfirm = isset($this->objRequest->_POST[Config::_HTTP_REQUEST_USER_EMAIL_CONFIRM]) ? $this->objRequest->_POST[Config::_HTTP_REQUEST_USER_EMAIL_CONFIRM] : "";
	
	$strUsername = isset($this->objRequest->_POST[Config::_HTTP_REQUEST_USER_USERNAME]) ? $this->objRequest->_POST[Config::_HTTP_REQUEST_USER_USERNAME] : "";
	
?>

<script>

	function onLoadFunction() {
		$("#menu_login").addClass("current_page_item");
	}
	
	window.onload = onLoadFunction;
</script>

<div id="main-wrapper">
	<div class="container">
   		
        <div id="registrationFormContainer">

            <p id="registrationFormTitle">Registra Lewe personale</p>
		
            <form name="registrationForm" action="/?<?php echo Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_REGISTRATION; ?>" method="post">
            
               <p class="registrationFormText">E-mail </p><input class="registrationFormInput" placeholder="E-mail" type="text" name="<?php echo Config::_HTTP_REQUEST_USER_EMAIL; ?>" value="<?php echo $strEmail?>" onkeydown="if(event.keyCode==13) document.forms['registrationForm'].submit();" />
                        
                <p class="registrationFormText registrationFormPasswordConfirmCorrection">Conferma E-mail </p><input class="registrationFormInput" placeholder="Conferma E-mail" type="text" name="<?php echo Config::_HTTP_REQUEST_USER_EMAIL_CONFIRM; ?>" value="<?php echo $strEmailConfirm?>" onkeydown="if(event.keyCode==13) document.forms['registrationForm'].submit();" />
                    
                <p class="registrationFormText">Username </p><input class="registrationFormInput" placeholder="Username" type="text" name="<?php echo Config::_HTTP_REQUEST_USER_USERNAME; ?>"  value="<?php echo $strUsername?>" onkeydown="if(event.keyCode==13) document.forms['registrationForm'].submit();" />
                        
                        
                <p class="registrationFormText">Password </p><input class="registrationFormInput" placeholder="Password" type="password" name="<?php echo Config::_HTTP_REQUEST_USER_PASSWORD; ?>" onkeydown="if(event.keyCode==13) document.forms['registrationForm'].submit();" />
                <p class="registrationFormText registrationFormPasswordConfirmCorrection">Conferma Password </p><input class="registrationFormInput" placeholder="Conferma Password" type="password" name="<?php echo Config::_HTTP_REQUEST_USER_PASSWORD_CONFIRM; ?>" onkeydown="if(event.keyCode==13) document.forms['registrationForm'].submit();" />
            	<div id="registrationFormButtonContainer">
                	<a class="button" id="registrationFormSubmit" onclick="document.forms['registrationForm'].submit();">Registrati</a>
                </div>
            </form>
    
			<?php
                //valutazione errori (possibile uso di css per segnalare errore)
                if ($blErrorMessage) {
            ?>
                <p id="loginFormErrorText"><?php echo $strErrorMessage; ?></p>
            <?php	
                }
            ?>
		</div>
	</div>
</div>