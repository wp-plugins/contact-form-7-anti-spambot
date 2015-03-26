<?php
/*
Plugin Name: Contact Form 7 Anti Spambot
Plugin URI: http://wordpress.org/plugins/contact-form-7-anti-spambot/
Description: No spam in the Contact Form 7.Add anti-spambot functionality to the CF7,it blocks spam without using CAPTCHA.To get started: 1) Click the 'Activate' link to the left of this description, 2) Edit a form in Contact Form 7, and insert the generated 'Anti-Spambot' tag anywhere in your form.
Author: SzMake
Version: 1.0.1
Author URI: http://www.szmake.net/
Text Domain: contact-form-7-anti-spambot
Domain Path: /languages/
License: GPLv3
*/

define('CF7ASB_VERSION', '1.0.1');
define('CF7ASB_DOMAIN', 'contact-form-7-anti-spambot');
define('CF7ASB_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
define('CF7ASB_KEYSEP', '-');
define('CF7ASB_HONEYPOTLBL', '-email-website-url');


$wpcf7asb_settings = array(
	'version' => CF7ASB_VERSION,
	'salt' => '1234567890',
	'debug' => 0,
);


include('cf7asb-admin.php');
include('cf7asb-functions.php');


add_action( 'init', 'wpcf7asb_init' );
function wpcf7asb_init() {
	 load_plugin_textdomain( CF7ASB_DOMAIN, false, basename( dirname( __FILE__ ) ).'/languages' );
}


add_action('wpcf7_init', 'wpcf7_antispambot_loader', 10);
function wpcf7_antispambot_loader() {
	if (function_exists('wpcf7_add_shortcode')) {
		wpcf7_add_shortcode( 'antispambot', 'wpcf7_antispambot_shortcode_handler', true );
	}
}

/* Shortcode handler */
function wpcf7_antispambot_shortcode_handler( $tag ) {
	global $wpcf7asb_settings;

	$tag = new WPCF7_Shortcode( $tag );

	if ( empty( $tag->name ) ){
		return '';
	}
	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type );

	if ( $validation_error ){
		$class .= ' wpcf7-not-valid';
	}

	$atts = array();
	$atts['class'] = $tag->get_class_option( $class );
	$atts['name'] = $tag->name;
	$atts['type'] = $tag->type;
	$atts['id'] = 'wpcf7asb-input';
	$atts['validation_error'] = $validation_error;


	$atts_honeypot = $atts;	// copy array
	$atts_honeypot['name'] = $atts_honeypot['name'].CF7ASB_HONEYPOTLBL;
	$atts_honeypot['id'] = $atts_honeypot['id'].CF7ASB_HONEYPOTLBL;

	$atts = wpcf7_format_atts( $atts );
	$atts_honeypot = wpcf7_format_atts( $atts_honeypot );
		
	// -----
	$html = '';
	$html .=  '<p class="wpcf7asb-input-block" id="wpcf7asb-input-block">';
	$html .=  __('Please enable javascript.', CF7ASB_DOMAIN);
	$html .=  '<br>';
	$html .=  __('Or you can post by following procedure.', CF7ASB_DOMAIN);
	$html .=  '<br>';
	$html .=  __('1.Please click on the link [GET TOKEN-CODE],then it is displayed.', CF7ASB_DOMAIN);
	$html .=  '<br>';
	$html .=  __('2.Enter displayed token-code to "TOKEN INPUT".', CF7ASB_DOMAIN);
	$html .=  '<br>';
	$html .=  '
		<label>[<a href="'.admin_url( "admin-ajax.php" ).'?action=wpcf7asb_currentkey&t='.time().'" target="wpcf7asb_iframe">'.__('GET TOKEN-CODE', SZMCF_DOMAIN).'</a>]</label>
		<iframe srcdoc="" name="wpcf7asb_iframe" width="100%" height="28px" marginwidth="2" marginheight="2" scrolling="auto" style="border: 2px gray solid;">
		</iframe>
		';
	$html .= sprintf(	'<span class="wpcf7-form-control-wrap %1$s">
						<span class="wpcf7-antispam-label">%2$s</span>&nbsp;
						<input %3$s />
						<span %4$s>
						<span class="wpcf7-antispam-label">%5$s</span>&nbsp;
						<input %6$s />
						</span>
						%7$s</span>',
						sanitize_html_class( $tag->name ),
						__('TOKEN INPUT', CF7ASB_DOMAIN),
						$atts,
						$wpcf7asb_settings['debug'] ?  'style="display:block;"' : 'style="display:none !important;visibility:hidden !important;"',
						__('Honeypot(Input unnecessary)', CF7ASB_DOMAIN),
						$atts_honeypot,
						$validation_error );

	$html .=  '</p>'.PHP_EOL;
	
	return $html;
}


/* Anti Spambot Validation Filter */
add_filter( 'wpcf7_validate_antispambot', 'wpcf7_antispambot_filter' ,10,2);

function wpcf7_antispambot_filter ( $result, $tag ) {
	$tag = new WPCF7_Shortcode( $tag );

	$name = $tag->name;

	$value = isset( $_POST[$name] ) ? $_POST[$name] : '';
	$name_honeypot = $name .CF7ASB_HONEYPOTLBL;
	$value_honeypot = isset( $_POST[$name_honeypot] ) ? $_POST[$name_honeypot] : '';

	$spam_flag = false;
	
	if(''!=$value_honeypot){
		$spam_flag = true;
		$spam_rules = 'honeypot[set data]';
		// result set
		$result['valid'] = false;
		$result['reason'] = array( $name => wpcf7_get_message( 'antispambot-honeypot' ) );
	} else if(''==$value){
		$spam_flag = true;
		$spam_rules = 'token[empty]';
		// result set
		$result['valid'] = false;
		$result['reason'] = array( $name => wpcf7_get_message( 'antispambot-empty' ) );
	} else if ( ! wpcf7asb_keychk($value) ) {
		$spam_flag = true;
		$spam_rules = 'token[invalid]';
		// result set
		$result['valid'] = false;
		$result['reason'] = array( $name => wpcf7_get_message( 'antispambot' ) );
	}
	
	if($spam_flag){
		// reg log
		$spam_req = array();
		$spam_req['at_blocked'] = current_time('Y/m/d H:i:s');
		$spam_req['ip'] = $_SERVER['REMOTE_ADDR'];
		$spam_req['rules'] = $spam_rules;
		$spam_req['wpcf7_id'] = $_POST['_wpcf7'];
		$post_data_array = array();
		$flg_was_input = false;
		foreach( $_POST as $key=>$value){
			if( '_wp'==substr($key, 0, 3) ){
				continue;
			}
			if(is_string($key) && is_string($value)){
				$reg_fieldname = $key;
				if( $name == $reg_fieldname ){
					$reg_fieldname = '[anti-spam token]';
				} else if( $name_honeypot == $reg_fieldname ){
					$reg_fieldname = '[anti-spam honeypot]';
				}
				$post_data_array[$reg_fieldname] = $value;

				if(strlen($value)>0){
					$flg_was_input = true;
				}
			}
		}
		$spam_req['post_data_array'] = $post_data_array;		
		
		// if all empty then no logreg...
		if($flg_was_input){
			wpcf7asb_reglog($spam_req);
		}

	}

	return $result;
}

/* Messages */

add_filter( 'wpcf7_messages', 'wpcf7_antispambot_messages' );

function wpcf7_antispambot_messages( $messages ) {
	return array_merge( $messages, array( 
		'antispambot' => array(	'description' => __("[anti-spambot]Sender doesn't enter the valid token-code.(when javascript disabled browser)", CF7ASB_DOMAIN),
								'default' => __('The input token is invalid.', CF7ASB_DOMAIN)
		),
		'antispambot-empty' => array(	'description' => __("[anti-spambot]Sender doesn't enter the token-code.(when javascript disabled browser)", CF7ASB_DOMAIN),
								'default' => __('The input token is empty.', CF7ASB_DOMAIN)
		),
		'antispambot-honeypot' => array(	'description' => __("[anti-spambot]Sender does enter the field of honeypot.(when css disabled browser)", CF7ASB_DOMAIN),
								'default' => __('The honeypot field is not input unnecessary.', CF7ASB_DOMAIN)
		),
		) );
}


// AjaxURL:header seg (ajax url def)
function wpcf7asb_add_my_ajaxurl() {
	global $wpcf7asb_settings;
?>
    <script>
        var wpcf7asb_ajaxurl = '<?php echo admin_url( "admin-ajax.php" ); ?>';
        <?php echo $wpcf7asb_settings['debug'] ? 'var wpcf7asb_debug = true;':'' ?>
    </script>
<?php
}
add_action( 'wp_head', 'wpcf7asb_add_my_ajaxurl', 1 );

/* js-include */
add_action( 'wpcf7_enqueue_scripts', 'wpcf7asb_enqueue_scripts' );
function wpcf7asb_enqueue_scripts() {
	$in_footer = true;
	if ( 'header' === WPCF7_LOAD_JS )
		$in_footer = false;

	wp_enqueue_script( 'contact-form-7-anti-spambot',
		wpcf7asb_plugin_url( 'js/cf7asb.js' ),
		array( 'jquery', 'jquery-form' ), CF7ASB_VERSION, $in_footer );

}

/* Ajax response */
add_action( 'wp_ajax_wpcf7asb_currentkey', 'wpcf7asb_ajax_currentkey' );
add_action( 'wp_ajax_nopriv_wpcf7asb_currentkey', 'wpcf7asb_ajax_currentkey' );
function wpcf7asb_ajax_currentkey(){
    echo wpcf7asb_keygen();
    die();
}


/* Tag generator */

add_action( 'admin_init', 'wpcf7_add_tag_generator_antispambot', 35 );

function wpcf7_add_tag_generator_antispambot() {
	if (function_exists('wpcf7_add_tag_generator')) {
		wpcf7_add_tag_generator( 'antispambot', 'Anti-Spambot',	'wpcf7-tg-pane-antispambot', 'wpcf7_tg_pane_antispambot' );
	}
}

function wpcf7_tg_pane_antispambot( $contact_form ) { 
	?>

	<div id="wpcf7-tg-pane-antispambot" class="hidden">
		<form action="">
			<table>
				<tr>
					<td>
						<?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?><br />
						<input type="text" name="name" class="tg-name oneline" /><br />
					</td>
					<td></td>
				</tr>
					
				<tr>
					<td>
						<code>class</code> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
						<input type="text" name="class" class="classvalue oneline option" />
					</td>
				</tr>				
			</table>
			
			<div class="tg-tag"><?php echo esc_html( __( "Copy this code and paste it into the form left.", 'contact-form-7' ) ); ?><br /><input type="text" name="antispambot" class="tag" readonly="readonly" onfocus="this.select()" /></div>
		</form>
	</div>

<?php }

?>