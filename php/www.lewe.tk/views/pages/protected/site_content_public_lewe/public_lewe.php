<?php

	use Config\Config;

?>
	
<script>

	function onLoadFunction() {
		$("#menu_public_lewe").addClass("current_page_item");
	}
	
	window.onload = onLoadFunction;
</script>

<div id="main-wrapper">
	<div class="container">
   		

		<div id="publicLeweContainer"> <!-- div che contiene gli utenti pubblicati -->

        	<p id="publicLeweTitle">Pubblica il tuo Lewe</p>
            
        	<div id="publicLeweUsersAllowedContainer">
            	<p class = "publicLeweSubTitle" id="publicLeweSubTitleAllowed">Username utenti autorizzati</p>
				<?php
                    
                    if ($arUserAllowed == array()) {
                ?>	
                <p id="publicLeweUserAllowTitle">Non hai pubblicato il tuo lewe</p>
                
                <?php
                    } else {
				?>
                
                <?php
                        foreach($arUserAllowed as $strUserAllowed) {
                ?>
                    
                    <div class="publicLeweUserAllowedContainer">
                        <p class="publicLeweUsername"><?php echo $strUserAllowed; ?></p>
                        <form name="form_<?php echo $strUserAllowed; ?>" action="/?<?php echo Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_PUBLIC_LEWE; ?>" method="post">
                            <input type="hidden" name="<?php echo Config::_HTTP_REQUEST_PUBLIC_LEWE_DIS; ?>" value="<?php echo $strUserAllowed; ?>" />
                        
                        </form>
                        <a class="publicLeweUserDelete button" onclick="document.forms['form_<?php echo $strUserAllowed; ?>'].submit();">-</a>
                    </div>
                <?			
                
                        }
                        
                    }
                ?>
			</div>
            <div id="publicLeweUsersToAllowContainer"> <!-- div che contiene il form per aggiungere utenti autorizzati -->
                <p class="publicLeweSubTitle" id="publicLeweSubTitleToAllow">Username utenti da autorizzare</p>
                
                <form id="publicLeweAutorizzationForm" name="autorizzationForm" action="/?<?php echo Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_PUBLIC_LEWE; ?>" method="post">
                    
                    <input type="hidden" name="<?php echo Config::_HTTP_REQUEST_PUBLIC_LEWE_N; ?>" value="3" />
                    
                    
                    <input class="publicLeweFormInput" placeholder="Username" type="text" name="<?php echo Config::_HTTP_REQUEST_PUBLIC_LEWE_KEY; ?>0" />
                    <input class="publicLeweFormInput" placeholder="Username" type="text" name="<?php echo Config::_HTTP_REQUEST_PUBLIC_LEWE_KEY; ?>1" />
                    <input class="publicLeweFormInput" placeholder="Username" type="text" name="<?php echo Config::_HTTP_REQUEST_PUBLIC_LEWE_KEY; ?>2" />
                    
                    <div id="publicLeweFormButtonContainer">
                		<a id="publicLeweFormSubmit" class="button" onclick="document.forms['autorizzationForm'].submit();">Autorizza</a>
               		</div>

                </form>
                
            </div>
		</div>
	</div>
</div>	
	