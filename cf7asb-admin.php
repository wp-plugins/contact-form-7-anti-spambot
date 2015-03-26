<?php

add_action( 'admin_enqueue_scripts', 'wpcf7asb_admin_enqueue_scripts' );
function wpcf7asb_admin_enqueue_scripts( $hook_suffix ) {
	global $wpcf7asb_settings;
	
	if ( false === strpos( $hook_suffix, 'wpcf7' ) )
		return;
	wp_enqueue_style(	CF7ASB_DOMAIN,
						wpcf7asb_plugin_url('css/styles-admin.css'),
						false,
						$wpcf7asb_settings['version'],
						'all'
						);
	wp_enqueue_script(	CF7ASB_DOMAIN,
						wpcf7asb_plugin_url('js/scripts-admin.js'),
						array('prototype'),
						$wpcf7asb_settings['version'],
						false
						);


}

function wpcf7asb_admin_notice() {
	global $plugin_page;
	
	if ( 'wpcf7' != $plugin_page || ! empty( $_GET['post'] ) ) {
		return;
	}

	$user_id = get_current_user_id();
	$wpcf7asb_info_visibility = get_user_meta($user_id, 'wpcf7asb_info_visibility', true);
	if ($wpcf7asb_info_visibility == 1 OR $wpcf7asb_info_visibility == ''){
		$blocked_total = wpcf7asb_get_logcount();
		$log_items = wpcf7asb_get_loglist();
		$log_itemcnt = 10;
		if($blocked_total<10){
			$log_itemcnt = $blocked_total;
		}
		
		?>

		<div class="update-nag wpcf7asb-panel-info">
			<p style="margin: 0;">
				<div style="overflow: hidden;">
				<div style="float: left;">
				<h4>CF7 Anti Spambot Report</h4>
				</div>
				<div style="float: right;">
				<p><a href="http://wp.szmake.net/donate/" class="button button-primary" target="_blank"><?php echo esc_html( __( 'Donate', 'contact-form-7' ) ) ?></a></p>
				</div>
				<div style="float: right; margin: 0px 8px;">
				<img src='<?php echo wpcf7asb_plugin_url( 'images/blkfrogman.png' ) ?>' width='48' height='48' align='top'>
				</div>
				</div>
				<?php echo sprintf( __( 'Total %s spam posts were blocked.', CF7ASB_DOMAIN), number_format( $blocked_total ) ); ?>
				<?php if($log_itemcnt>0): ?>
				<form method="post" style="padding: 20px 0 5px 0;">
					<input type="hidden" name="wpcf7asb_option_submit" value="9" />
					<input type="submit" class="button" value="<?php _e('Blocked Count Reset', CF7ASB_DOMAIN); ?>" onclick='return confirm("<?php _e('Are you sure you want to reset?', CF7ASB_DOMAIN) ?>")' />
				</form>
				<?php endif; ?>
			</p>
			<?php if($log_itemcnt>0): ?>
			<?php echo sprintf( __( '[The blocked post of latest %d cases]', CF7ASB_DOMAIN), number_format( $log_itemcnt ) ); ?>
			<div id="wpcf7asb_loglist">
				<?php
				$logno = 0;
				foreach($log_items as $log_item):
				$logno++;
				?>
				<div class="dathead">
						<div class="dhead1">#<?php echo $logno ?>&nbsp;blocked at <?php echo $log_item['at_blocked'] ?></div>
						<div class="dhead2">[From IP : <?php echo $log_item['ip'] ?>]</div>
						<div class="dhead3">[contact-form-7 id="<?php echo $log_item['wpcf7_id'] ?>"]</div>
						<div class="dhead4">Rules :  <?php echo $log_item['rules'] ?></div>
				</div>
				<div class="ditail">
					<table>
						<tr>
							<th class="field_name">field name</th>
							<th>input data</th>
						</tr>
						<?php 
						foreach( $log_item['post_data_array'] as $key=>$value){
						?>
							<tr>
								<td class="post_key"><?php echo htmlspecialchars($key) ?></td>
								<td class="post_value"><?php echo htmlspecialchars($value) ?></td>
							</tr>
						<?php } ?>
					</table>
				</div>
				<?php
				endforeach;
				?>
			</div>​


			<?php endif; ?>
		</div>
		<?php
	}

}
add_action('wpcf7_admin_notices', 'wpcf7asb_admin_notice');


function wpcf7asb_display_screen_option() {
	
	global $plugin_page;
	
	if ( 'wpcf7' != $plugin_page || ! empty( $_GET['post'] ) ) {
		return;
	}

	$user_id = get_current_user_id();
	$wpcf7asb_info_visibility = get_user_meta($user_id, 'wpcf7asb_info_visibility', true);

	if ($wpcf7asb_info_visibility == 1 OR $wpcf7asb_info_visibility == '') {
		$checked = 'checked="checked"';
	} else {
		$checked = '';
	}

	?>
	<script>
		jQuery(function($){
			$('.wpcf7asb_screen_options_group').insertAfter('#screen-options-apply');
		});
	</script>
	<form method="post" class="wpcf7asb_screen_options_group" style="padding: 20px 0 5px 0;">
		<h5>CF7 Anti Spambot</h5>
		<input type="hidden" name="wpcf7asb_option_submit" value="1" />
		<label>
			<input name="wpcf7asb_info_visibility" type="checkbox" value="1" <?php echo $checked; ?> />
			<?php _e('Show Report', CF7ASB_DOMAIN); ?>
		</label>
		<input type="submit" class="button" value="<?php _e('Apply'); ?>" />
	</form>
	<?php

	return;

}

function wpcf7asb_register_screen_option() {
	add_filter('screen_layout_columns', 'wpcf7asb_display_screen_option');
	
}
add_action('admin_head', 'wpcf7asb_register_screen_option');





function wpcf7asb_update_screen_option() {

	if (isset($_POST['wpcf7asb_option_submit'])){
		if ( $_POST['wpcf7asb_option_submit'] == 1) {
			$user_id = get_current_user_id();
			if (isset($_POST['wpcf7asb_info_visibility']) AND $_POST['wpcf7asb_info_visibility'] == 1) {
				update_user_meta($user_id, 'wpcf7asb_info_visibility', 1);
			} else {
				update_user_meta($user_id, 'wpcf7asb_info_visibility', 0);
			}
		} else if($_POST['wpcf7asb_option_submit'] == 9){
			// do log zero reset.
			wpcf7asb_clear_logdata();
		}
	}
	
}
add_action('admin_init', 'wpcf7asb_update_screen_option');
