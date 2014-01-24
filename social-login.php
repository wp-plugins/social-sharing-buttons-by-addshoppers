<?php
/*
Social Login functions
since 1.3
*/

function addshoppers_show_social_login($networks,$size) {
	if ($networks) $buttons = explode(',',str_replace(" ","",$networks));
	else $buttons = 'facebook';
	
	if ($size != 'small' && $size != 'medium' && $size != 'large') $size = "medium";

	echo '<div class="addshoppers-social-login-buttons" style="text-align: center;">';

	foreach ($buttons as $button) {
		echo '<div class="social-commerce-signin-' . $button . '" data-style="logoandtext" data-size="' . $size . '" style="display: inline-block;"></div>';
	}
	
	echo '</div>';
	
	addshoppers_social_login_js();
}

function addshoppers_social_login_js() {
	?>
	<script type="text/javascript">
	function init() {
   		AddShoppersWidget.API.Event.bind("sign_in", createAccount);		
	};
	function createAccount(params) {	
		if (params.source == "social_login") {	
			var data = AddShoppersWidget.API.User.signed_data();
			if ("google_picture" in data) {
				data.google_picture = data.google_picture.replace('http', 'h--p');
			}
			//window.location.href = '<?php echo site_url(); ?>/?action=addshoppers_social_login&as_signature=' + JSON.stringify(data) + '&redirect_to=' + encodeURIComponent(window.location.href);
			postLoginData(JSON.stringify(data));
		}
	}
	if (window.addEventListener) {			
	   window.addEventListener("load", init, false); 
	} else {
	   document.onreadystatechange = function() { 
	    if(document.readyState in {loaded: 1, complete: 1}) {
		document.onreadystatechange = null; 
	       init();			
	    } 
	  }					
	} 
	function postLoginData(data) {
 
  		// Send the data using post
  		var posting = jQuery.post( '<?php echo site_url("/"); ?>', { action: 'addshoppers_social_login', as_signature: data } );
 
  		// Put the results in a div
  		posting.done(function(data) {
    		// refresh page
    		location.reload();
  		});
	}
	</script>
	<?php
}

/*
Social Login Shortcode
*/

function addshoppers_social_login_shortcode( $atts ){
	$stuff = shortcode_atts( array(
		'networks' => 'facebook,',
		'size' => 'medium',
	), $atts );

	addshoppers_show_social_login($stuff['networks'],$stuff['size']);

	return;
}
add_shortcode( 'AddShoppersSocialLogin', 'addshoppers_social_login_shortcode' );

function addshoppers_social_login() {

	if( ! isset( $_REQUEST[ 'action' ] ) || $_REQUEST[ 'action' ] !=  "addshoppers_social_login" ){
		return;
	}

	if ( isset( $_REQUEST[ 'redirect_to' ] ) && $_REQUEST[ 'redirect_to' ] != '' ){
		$redirect_to = $_REQUEST[ 'redirect_to' ];

		// Redirect to https if user wants ssl
		if ( isset( $secure_cookie ) && $secure_cookie && false !== strpos( $redirect_to, 'wp-admin') ){
			$redirect_to = preg_replace( '|^http://|', 'https://', $redirect_to );
		}
	}

	if( empty( $redirect_to ) ){
		$redirect_to = site_url();
	}

	$user_id = null;
	$options = get_option( 'shop_pe_options' );
	
	if ( empty( $options['api_secret'] ) ) {
		return;
	}
	
	$response = addshoppers_verify_data($_POST['as_signature'],$options['api_secret']);
	
	if ($response['error']) { 
		return;
	}

	// check if there is an account under the email address
	$user_id = (int) email_exists( $response['email'] );

	// if user found
	if( $user_id ){
		$user_data  = get_userdata( $user_id );
		$user_login = $user_data->user_login;
	} 
	else {
		$userdata = array(
			'user_login'    => $response['email'],
			'user_email'    => $response['email'],
			'display_name'  => $response['firstname'] . " " . $response['lastname'],
			'first_name'    => $response['firstname'],
			'last_name'     => $response['lastname'], 
			'user_pass'     => wp_generate_password()
		);
		
		// set role to be "customer" for WooCommerce
		if (woocommerce_is_installed()) {
			$userdata['role'] = "customer";
			$woo = TRUE;
		}
		
		$user_id = wp_insert_user( $userdata );
		
		if( $user_id && is_integer( $user_id ) ){
			update_user_meta( $user_id, 'Source', 'AddShoppers Social Login' );
			
			// set Billing & Shipping First & Last names for WooCommerce
			if ($woo) {
				add_user_meta( $user_id, 'billing_first_name', $response['firstname']);
				add_user_meta( $user_id, 'shipping_first_name', $response['firstname']);
				add_user_meta( $user_id, 'billing_last_name', $response['lastname']);
				add_user_meta( $user_id, 'shipping_last_name', $response['lastname']);
			}
		}
		else if (is_wp_error($user_id)) { 
			echo $user_id->get_error_message();
		}
		else{
			die( "We ran into an error while creating a new user!" );
		}
	}
	
    wp_set_auth_cookie( $user_id );
		
	//wp_safe_redirect( $redirect_to );

	exit();
}

add_action( 'init', 'addshoppers_social_login' );





if (!function_exists('addshoppers_verify_data')){
	function addshoppers_verify_data($data,$api_secret) {
		$params = json_decode(urldecode(stripslashes($data)), true);
		$signature = null;
		$p = array();
		
		if ($params['google_picture']) $params['google_picture'] = str_replace('h--p', 'http', $params['google_picture']);
  		
		foreach($params as $key => $value)
		{
    		if($key == "signature")
        		$signature = $value;
   		 	else
		    	$p[] = $key . "=" . $value;
		}
		asort($p);
		$query = $api_secret . implode($p);
		$hashed = hash("md5", $query);
		if($signature !== $hashed) return array('error' => 1); 
		else return get_profile_info($params); 
	}
}

if (!function_exists('get_profile_info')){
	function get_profile_info($data) {
		$networks = array('facebook','google','paypal','linkedin','twitter');
		$profile_data = array();
				
		foreach ($networks as $network) {
			if ($data[$network . '_email'] && !$profile_data['email'])
				$profile_data['email'] = $data[$network . '_email'];
			if ($data[$network . '_firstname'] && !$profile_data['firstname'])
				$profile_data['firstname'] = $data[$network . '_firstname'];
			if ($data[$network . '_lastname'] && !$profile_data['lastname'])
				$profile_data['lastname'] = $data[$network . '_lastname'];
		}
	return $profile_data;
	}
}

?>