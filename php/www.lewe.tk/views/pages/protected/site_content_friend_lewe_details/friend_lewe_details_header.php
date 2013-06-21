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
   		

		<div id="friendDetailsContainer"> <!-- div che contiene gli utenti pubblicati -->
        	
            <p id="friendDetailsLeweTitle">Lewe di <?php echo $objUserShow->Username; ?></p>