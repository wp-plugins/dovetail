<div class="wrap">
	<?php
		echo "<div id=\"icon-options-general\" class=\"icon32\"><br></div><h2>" . __( 'FP Members', 'fp-members' ) . " ". __( 'Page Settings', 'fp-members' ) . "</h2>";
	?>
	<form name="form1" method="post" action="">
		<input type="hidden" name="data_submitted" value="Y">
		
		<p>
			<label for="restricted_page">If denied access, redirect to this page</label>
			
			<?php
				$pages = get_pages();
			?>
			<select id="restricted_page" name="restricted_page_id">
				<option> -- Select Page -- </option>
				<?php
					foreach ($pages as $index => $page) {
						if ( $restricted_page_id == $page->ID ) {
							echo '<option value="'.$page->ID.'" selected>'.$page->post_title."</option>";
						} else {
							echo '<option value="'.$page->ID.'">'.$page->post_title."</option>";
						}
					}
				?>
			</select>
		</p>
		
		<p>
			<label for="restricted_page">With this message</label>
			
			<input type="text" value="<?php echo $restricted_message ?>" name="restricted_message" />
		</p>

		<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
		</p>

	</form>
	
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			if ( $('#title').val() != '' ) {
				$('#title').parent().find("label").css({ "display" : "none" });
			}
			
			$('#title').keypress(function() {
				if ( $(this).val() != '' ) {
					$(this).parent().find("label").css({ "display" : "none" });
				}
			})
		});
	</script>
</div>