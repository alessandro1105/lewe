<?php
	use \Config\Config;
?>

<script>

	function onLoadFunction() {
		$("#menu_homepage").addClass("current_page_item");
	}
	
	window.onload = onLoadFunction;
</script>

<!-- Main -->

<div id="main-wrapper">
	<div class="container">
    
        <div id="home_page">
        	
            
            
            
            <div class="row">
				<div class="12u">
					<div id="banner">
						<a href="/"><img src="images/logo.png" alt="" /></a>
                        
                        
						<div class="caption">
							<span><strong>Lewe</strong>: il bracciale biometrico rivoluzionario</span>
							<a href="/?<?php echo Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_HOWITWORKS; ?>" class="button">Scoprilo!</a>
                        </div>
                        
                        
					</div>
				</div>
			</div>
            

       
            <div class="row">
				<div class="12u">
					<div class="highlight-box">
						<h2>Vuoi avere sotto controllo la tua salute?</h2>
						<span>Lewe è la soluzione perfetta!</span>
					</div>
				</div>
			</div>
            
            
             <div class="row">
				<div class="12u">
					<div class="cta-box">
						<span>Un progetto totalmente Open Source</span>
						<a href="/?<?php echo Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_CODE; ?>" class="button">Scarica codice</a>
					</div>
				</div>
			</div>
            
        </div>
         
	</div>
</div>
