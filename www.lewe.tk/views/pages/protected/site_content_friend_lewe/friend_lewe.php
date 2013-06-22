<?php
	
	use Config\Config;

?>

<script>

	function onLoadFunction() {
		$("#menu_friend_lewe").addClass("current_page_item");
	}
	
	window.onload = onLoadFunction;
</script>

<div id="main-wrapper">
	<div class="container">
   		

		<div id="friendLeweContainer"> <!-- div che contiene gli utenti pubblicati -->
        	
            <p id="friendLeweTitle">Lewe degli amici</p>

			<?php
                
                if ($arUserAllowedUsername == array()) {
            ?>	
            
                <p id="friendLeweUserAllowTitle">Non sei autorizzato a visualizzare il lewe di nessun utente!</p>
                
            <?php
                } else {
                    
                    for($i = 0; $i < count($arUserAllowedUsername); $i++) {
            ?>
                    
                    <div class="friendLeweFriendContainer">
                        <form name="form_<?php echo $arUserAllowedUsername[$i]; ?>" action="/?<?php echo Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_FRIEND_LEWE_DETAILS; ?>" method="post">
                            
                            <input type="hidden" name="<?php echo Config::_HTTP_REQUEST_LEWE_USER_ID ?>" value="<?php echo $arUserAllowedId[$i]; ?>" />
                        
                        </form>
                        
                        <a class="friendLeweFriendText" onclick="document.forms['form_<?php echo $arUserAllowedUsername[$i]; ?>'].submit();"><?php echo $arUserAllowedUsername[$i]; ?></a>
                    </div>
                            
            <?php		
                    }
                    
                }
            ?>
        </div>
	</div>
</div>