<div class="buttons">
	<form action="<?php echo $futubank_url; ?>" method="post" id="futubank-form">
		<?php foreach ($futubank_form as $k => $v) { ?>
			<input name="<?php echo htmlspecialchars($k); ?>" value="<?php echo htmlspecialchars($v); ?>" type="hidden">	
		<?php } ?>
		<div class="right">
	    	<button type="submit" class="button"><span><?php echo $button_confirm; ?></span></button>
	 	</div>
 	</form>
</div>
