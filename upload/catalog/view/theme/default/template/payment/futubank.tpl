<div class="buttons">
	<form action="<?php echo $futubank_url; ?>" method="post" id="futubank-form">
		<?php foreach ($futubank_form as $k => $v) { ?>
			<input name="<?php echo htmlspecialchars($k); ?>" value="<?php echo htmlspecialchars($v); ?>" type="hidden">	
		<?php } ?>
		<div class="right">
			time()=<?= time() ?>=
	    	<input type="submit" class="button" value="<?php echo htmlspecialchars($button_confirm); ?>">
	 	</div>
 	</form>
</div>
