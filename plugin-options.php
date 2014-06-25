<?php
/**
 * AddShoppers Plugin Options
 *
 * @package WPShopPe
 * @version 1.1
 */

if ( ! function_exists( 'shop_pe_plugin_admin_init' ) ):
/**
 * Initializes plugin options.
 *
 * @since WPShopPe 1.0
 */
function shop_pe_plugin_admin_init() {
    global $plugin_page;
    
    register_setting(
        'shop_pe_plugin_options',
        'shop_pe_options',
        'shop_pe_plugin_admin_validate'
    );
}
endif;
add_action( 'admin_init', 'shop_pe_plugin_admin_init' );

if ( ! function_exists( 'shop_pe_plugin_admin_add_page' ) ):
/**
 * Registers plugin options page.
 *
 * @since WPShopPe 1.0
 */
function shop_pe_plugin_admin_add_page() {
    add_options_page(
        'AddShoppers',
        'AddShoppers',
        'edit_theme_options',
        'shop-pe-plugin',
        'shop_pe_plugin_admin_do_page'
    );
}
endif;
add_action( 'admin_menu', 'shop_pe_plugin_admin_add_page' );

/*
 * Add submenu to main WooCommerce menu
 *
 */
add_action( 'admin_menu', 'addshoppers_woocommerce_submenu' );
function addshoppers_woocommerce_submenu() {
    add_submenu_page( 'woocommerce', 'AddShoppers', 'AddShoppers', 'edit_theme_options', 'shop-pe-plugin', 'shop_pe_plugin_admin_do_page' ); 
}

/**
 * Include admin CSS (only on the plugin settings page).
 *
 * @since WPShopPe 1.0
 */
function addshoppers_admin_css($hook) {
    if( 'settings_page_shop-pe-plugin' != $hook && 'woocommerce_page_shop-pe-plugin' != $hook)
        return;
    wp_enqueue_style( 'addshoppers_admin_css', AS_PLUGIN_FOLDER . 'addshoppers-admin.css' );
}
add_action( 'admin_enqueue_scripts', 'addshoppers_admin_css' );

/**
 * Include responsive CSS
 *
 * @since WPShopPe 1.2
 */
 
 /*
 Removing responsive CSS because it's already included in the widget now - version 1.5.8
 
function addshoppers_responsive_css($hook) {
    wp_enqueue_style( 'addshoppers_responsive_css', AS_PLUGIN_FOLDER . 'addshoppers-responsive.css' );
}
add_action( 'wp_enqueue_scripts', 'addshoppers_responsive_css' );

*/

if ( ! function_exists( 'shop_pe_plugin_admin_do_page' ) ):
/**
 * Renders admin plugin options page.
 *
 * @since WPShopPe 1.0
 */
function shop_pe_plugin_admin_do_page() {
    $options = get_option( 'shop_pe_options' );
    if ( empty( $options ) ) {
        $options = array(
            'shop_id' => '',
            'default_buttons' => 1,
            'selected_networks' => addshoppers_networks('default')
        );
    }
    $view = $_GET['view'];
    if ($view == "dashboard") $tab = "dashboard";
    else if ($view == "settings") $tab = "settings";
    else if (empty($options['shop_id'])) $tab = "settings";
    else $tab = "dashboard";
?>
    <div class="wrap addshoppers">
    
    	<a class="orange_callout" href="http://www.addshoppers.com/apps" target="_blank">Selling online? Check out more of our apps!</a>
    	
        <div id="addshoppers_icon" style="background-image: url(<?php echo AS_PLUGIN_IMG_FOLDER . 'icon.png'; ?>);">
        	<h2>AddShoppers</h2>
        </div>
		
		<?php 
			// show nav tab and content
			show_as_nav_tab($tab); 
			if ($tab == "settings") show_settings_form($options); 
			else if ($tab == "dashboard") show_as_dashboard($options['shop_id']);
		?>
		
    </div>
<?php
}
endif;

function show_as_nav_tab($tab) {
	$tabs = array(
		'dashboard' => 'Dashboard',
		'settings' => 'Settings'
	);
?>
        <div class="nav-tab-wrapper">
        	<?php foreach ($tabs as $key => $display) { ?>
        		<a style="background-image: url(<?php echo AS_PLUGIN_IMG_FOLDER . $key . '-icon.png'; ?>);" class="nav-tab as-nav-tab-<?php echo $key; ?><?php if ($tab == $key) echo ' nav-tab-active'; ?>" href="?page=shop-pe-plugin&view=<?php echo $key; ?>"><?php echo $display; ?></a> 
        	<?php } ?>
        		<a style="background-image: url(<?php echo AS_PLUGIN_IMG_FOLDER . 'support-icon.png'; ?>);" class="nav-tab as-nav-tab-support" href="http://help.addshoppers.com" target="_blank">Support</a> 
		</div>
<?php 
}

function show_as_dashboard($shop_id) {
	if (empty($shop_id)) { 
?>
		<h3>You haven't set up your account so we can't track stats for you :(</h3>
		<p>To see your dashboard and social analytics here instead of this message, please <a href="https://www.addshoppers.com/merchants" target="_blank">click here</a> to create your account. Once you've created your account, go to Settings > Shops and copy the Shop ID for your site into the Settings tab above.</p>
	<?php } else { ?>
	<iframe id="dashboard_iframe" src="https://app.addshoppers.com/login/" style="border: 0px; margin-top: 10px;<?php if (empty($shop_id)) echo 'display: none;'; ?>" height="600px" width="100%;" />
	<?php
	}
}

function show_settings_form($options) {
    $all_networks = addshoppers_networks('all');
    if (!$options['selected_networks']) $options['selected_networks'] = addshoppers_networks('default'); 
    if (!$options['login_networks']) $options['login_networks'] = array(); 
?>
        <form method="post" action="options.php" class="settings">
            <?php settings_fields( 'shop_pe_plugin_options' ); ?>
            
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">Shop ID</th>
                        <td>
                            <input id="shop-id" class="regular-text" type="text" name="shop_pe_options[shop_id]" value="<?php echo( $options['shop_id'] ); ?>" placeholder="Your ID" />
                            <p class="description">(Optional) Enter your shop ID if you want to track the analytics of your sharing buttons. <br/>You can get your shop ID or sign up for one <a href="https://www.addshoppers.com/merchants" target="_blank">here</a>. Go to your profile (top right), choose Shop Settings and copy the Shop ID for your shop into the field above.</p>
                        </td>
                     </tr>
                     <tr>
                        <th scope="row">API Secret</th>
                        <td>
                            <input id="api-secret" class="regular-text" type="text" name="shop_pe_options[api_secret]" value="<?php echo( $options['api_secret'] ); ?>" placeholder="Your API Secret" />
                            <p class="description">(Only necessary for AddShoppers Social Login) <br/>You can get your API Secret from your <a href="https://www.addshoppers.com/merchants" target="_blank">AddShoppers dashboard</a>. Go to your profile (top right), choose Account Settings, then select the API tab and copy the API Secret (not API Key) for your shop into the field above</p>
                        </td>
                     </tr>
                      <tr>
                        <th scope="row">Show Floating Buttons</th>
                        <td>
                            <input id="default-buttons" type="checkbox" name="shop_pe_options[default_buttons]" value="1" <?php if ($options['default_buttons'] == 1 ) echo 'checked="checked" '; ?>/>
                            <p class="description">Check this box to show the default floating buttons. If you want different buttons, grab the code for the buttons you want in your <a href="https://www.addshoppers.com/merchants" target="_blank">AddShoppers Dashboard</a> under Apps &rarr; Sharing Buttons. Copy and paste the code for your buttons into the desired location in your active theme.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Choose Networks</th>
                        <td>
                        	<?php foreach ($all_networks as $key => $display) { ?>
                        		<input id="selected-networks-<?php echo $key; ?>" class="network_select <?php echo $key; ?>" type="checkbox" name="shop_pe_options[selected_networks][]" value="<?php echo $key; ?>" <?php if (in_array($key,$options['selected_networks'])) echo 'checked="checked" '; ?>/> <label for="selected-networks-<?php echo $key; ?>"></label>
                        	<?php } ?>
                            
                            <p class="description">Select the networks that you want in your sharing button set.</p>
                        </td>
                    </tr>
	<?php if (woocommerce_is_installed()) { ?>         
                    <tr>
                        <th scope="row">Show Share for Coupon Button on Cart Page</th>
                        <td>
                            <input id="show-coupon-button-woocommerce-cart" type="checkbox" name="shop_pe_options[show_coupon_button_woocommerce_cart]" value="1" <?php if ($options['show_coupon_button_woocommerce_cart'] == 1 ) echo 'checked="checked" '; ?>/>
                            <p class="description">Check this box to show a Share for Coupon button right below your Enter Coupon Code box (great for increasing conversions!). Make sure you set up a Social Reward (<a href="http://help.addshoppers.com/customer/portal/articles/688052-how-to-add-a-social-reward-to-addshoppers-" target="_blank">instructions</a>) first!</p>
                        </td>
                    </tr>
    <?php } ?>
                    <tr>
                        <th scope="row">ROI Tracking</th>
                        <td>
                        	<?php 
                        	// if WooCommerce is detected
                        	if (woocommerce_is_installed()) {
                        		$ecom = true; 
                        		show_roi_tracking_admin('WooCommerce', $options);
                        	 } 
                        	// if eShop is detected
                        	if (eshop_is_installed()) {
                        		$ecom = true; 
                        		show_roi_tracking_admin('eShop', $options);
                        	}                      	
                        	// if no eCommerce plugins detected
                        	if (!$ecom) { ?>
                        		<p class="description">We haven't detected an eCommerce plugin installed on your WordPress site that we have an automatic integration with. If you want to track ROI, please install manually. Check <a target="_blank" href="http://help.addshoppers.com/customer/portal/topics/315555-installation/articles">here</a> for installation instructions or <a href="http://help.addshoppers.com" target="_blank">contact us</a> if you need help.</p>
                        	<?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Social Login</th>
                        <td>
                        	<?php 
                        	// if WooCommerce is detected
                        	if (woocommerce_is_installed()) {
                        		$lower_name="woocommerce";
                        		$login_networks = addshoppers_networks('login');
                        		?>
                        		
                        		<p style="margin-bottom: 10px;"><input id="show-<?php echo $lower_name; ?>-social-login" type="checkbox" name="shop_pe_options[show_<?php echo $lower_name; ?>_social_login]" value="1" <?php if ($options['show_' . $lower_name . '_social_login'] == 1 ) echo 'checked="checked" '; ?>/> Show social login buttons above login/register form</p>
                        		                        		
                        		<?php foreach ($login_networks as $key => $display) { ?>
                        		<input id="login-networks-<?php echo $key; ?>" class="network_select <?php echo $key; ?>" type="checkbox" name="shop_pe_options[login_networks][]" value="<?php echo $key; ?>" <?php if (in_array($key,$options['login_networks'])) echo 'checked="checked" '; ?>/> <label for="login-networks-<?php echo $key; ?>"></label>
                        		<?php } ?>
                        		<p class="description">Select which networks to offer social login.</p>
                        		
                        	<?php
                        		/*	
                        		<p><input id="show-<?php echo $lower_name; ?>-social-login-login" type="checkbox" name="shop_pe_options[show_<?php echo $lower_name; ?>_social_login_login]" value="1" <?php if ($options['show_' . $lower_name . '_social_login_login'] == 1 ) echo 'checked="checked" '; ?>/> Show above login form</p>
                        		                        		
                        		<p><input id="show-<?php echo $lower_name; ?>-social-login-registration" type="checkbox" name="shop_pe_options[show_<?php echo $lower_name; ?>_social_login_registration]" value="1" <?php if ($options['show_' . $lower_name . '_social_login_registration'] == 1 ) echo 'checked="checked" '; ?>/> Show above registration form</p>
                        		*/
                        	?>
                        	                        		                        		                        		
                        		<?php
                        	}                   	
                        	// if no eCommerce plugins detected
                        	if (!$ecom) { ?>
                        		
								<p class="description">To install Social Login, use the following shortcode or PHP function in your login/registration page templates: </p>
								<p style="font-weight: bold;">Shortcode:</p>
								<pre>[AddShoppersSocialLogin networks="facebook,google,paypal,linkedin" size="medium"]</pre>
								<p style="font-weight: bold;">PHP function:</p>
								<pre>addshoppers_show_social_login("facebook,google,paypal,linkedin","medium")</pre>
								<p>You can remove the networks that you don't want and/or change the size to either "small" or "large".</p>
								<p style="margin-top: 15px;"><b>Here's an example to show Facebook and Google+ social login buttons, large size:</b></p>
								<pre>&lt;?php addshoppers_show_social_login(&#39;facebook,google&#39;,&#39;large&#39;); ?&gt;</pre>
                        	<?php } ?>
                        	
                        	<p class="description">Make sure you have your API Secret set above or social login won't work!</p>
                        </td>
                    </tr>
	<?php 
     // if WooCommerce is detected
     if (woocommerce_is_installed()) { 
     ?>
                    <tr>
                        <th scope="row">Purchase Sharing</th>
                        <td>
                        	<p style="margin-bottom: 10px;"><input id="default-buttons" type="checkbox" name="shop_pe_options[purchase_sharing]" value="1" <?php if ($options['purchase_sharing'] == 1 ) echo 'checked="checked" '; ?>/> Check this box to enable Purchase Sharing.</p>
                        		
                        		<h3>Default Purchase Sharing Information</h3>
                        		
                        		<p>If there is <b>one product in the order</b>, the details of the product will be shared on the Thank You page. If there are <b>more than one products</b> in the order, this information will be shared instead:</p>
                        		
                        		<input style="margin-top: 10px;" id="purchase-sharing-header" class="regular-text" type="text" name="shop_pe_options[purchase_sharing_header]" value="<?php echo( $options['purchase_sharing_header'] ); ?>" placeholder="Sharing Modal Header" />
                            	<p class="description">Enter the header for your Purchase Sharing header. For example, "Brag about your purchase to your friends!"</p>          
                            	
                            	<input style="margin-top: 10px;" id="purchase-sharing-image" class="regular-text" type="text" name="shop_pe_options[purchase_sharing_image]" value="<?php echo( $options['purchase_sharing_image'] ); ?>" placeholder="Share Image URL" />
                            	<p class="description">Enter the URL to the image that you want shared. Generally, this will be your logo.</p>          
                            	
                            	<input style="margin-top: 10px;" id="purchase-sharing-url" class="regular-text" type="text" name="shop_pe_options[purchase_sharing_url]" value="<?php echo( $options['purchase_sharing_url'] ); ?>" placeholder="Share URL" />
                            	<p class="description">Enter the URL that you want shares to link to. Generally, this will be your homepage. </p>          
                            	
                            	<input style="margin-top: 10px;" id="purchase-sharing-name" class="regular-text" type="text" name="shop_pe_options[purchase_sharing_name]" value="<?php echo( $options['purchase_sharing_name'] ); ?>" placeholder="Share Title" />
                            	<p class="description">Enter the title for the share. Generally, this will be your store title.</p>          
                            	
                            	<input style="margin-top: 10px;" id="purchase-sharing-description" class="regular-text" type="text" name="shop_pe_options[purchase_sharing_description]" value="<?php echo( $options['purchase_sharing_description'] ); ?>" placeholder="Share Description" />
                            	<p class="description">Enter the description for your share. Generally, this will be a description of your store.</p>                  		
                        	
                        </td>
                    </tr>
			<?php
				}                   	
			?>
                </tbody>
            </table>

            <p class="submit">
                <input type="submit" name="submit" value="Save" class="addshoppers-submit" style="background-image: url(<?php echo AS_PLUGIN_IMG_FOLDER . 'button.png'; ?>); ">
            </p>
        </form>
<?php
}

function addshoppers_networks($which) {
	$networks = array();
	$networks['all'] = array(
		'facebook' => 'Facebook',
		'twitter' => 'Twitter',
		'google' => 'Google Plus',
		'email' => 'Email',
		'pinterest' => 'Pinterest',
		'stumbleupon' => 'StumbleUpon',
		'tumblr' => 'Tumblr',
		'wanelo' => 'Wanelo',
		'polyvore' => 'Polyvore',
		'kaboodle' => 'Kaboodle'
	);
	$networks['default'] = array(
		'facebook',
		'twitter',
		'email',
		'google'
	);
	$networks['login'] = array(
		'facebook' => 'Facebook',
		'google' => 'Google Plus',
		'paypal' => 'Paypal',
		'linkedin' => 'Linkedin'
	);
	return $networks[$which];
}

if ( ! function_exists( 'shop_pe_plugin_admin_validate' ) ):
/**
 * Validates data from the form.
 *
 * @since WPShopPe 1.0
 */
function shop_pe_plugin_admin_validate( $input ) {
    $options = get_option( 'shop_pe_options' );
    if ( empty( $options ) ) {
        $options = array(
            'shop_id' => '',
            'default_buttons' => 1,
            'selected_networks' => addshoppers_networks('default')
        );
    }

    $message = 'Options saved.';
    $type = 'updated';

    if ( empty( $input ) ) {
        $message = 'You must provide options.';
        $type = 'error';
    } else {
    	// validating shop ID
        $shop_id = strtolower( $input['shop_id'] );
        if ( preg_match( '/^[0-9a-f]{24}$/', $shop_id ) == 0 && !empty($shop_id) ) {
            $message = 'Invalid Shop ID: ' . $input['shop_id'];
            $type = 'error';
        }
        else {
            $options['shop_id'] = $shop_id;
        }
    }
	
	$options['api_secret'] = $input['api_secret'];
	
	// default buttons
    if (!$input['default_buttons']) $input['default_buttons'] = 0;
    $options['default_buttons'] = $input['default_buttons'];
    
    // networks to show
    if (empty($input['selected_networks'])) {
    	$message = 'You must select at least 1 network for your sharing buttons. To disable the default sharing buttons, uncheck the "Show Floating Buttons?" checkbox.';
        $type = 'error';
    }
    else {
    	$all_networks = addshoppers_networks('all');
    	$options['selected_networks'] = array();
    	foreach ($input['selected_networks'] as $network) {
    		if (array_key_exists($network,$all_networks)) {
    			$options['selected_networks'][] = $network;
    		}
    	}
    }
    
    // WooCommerce Share for Coupon button
    $options['show_coupon_button_woocommerce_cart'] = $input['show_coupon_button_woocommerce_cart'];
    
    // social login buttons to show
	$login_networks = addshoppers_networks('login');
    $options['login_networks'] = array();
    
    if (is_array($input['login_networks'])) {
		foreach ($input['login_networks'] as $network) {
    		if (array_key_exists($network,$login_networks)) {
    			$options['login_networks'][] = $network;
    		}
    	}
    }

    // social login integrations
    $options['show_woocommerce_social_login'] = $input['show_woocommerce_social_login'];
    //$options['show_woocommerce_social_login_login'] = $input['show_woocommerce_social_login_login'];
   // $options['show_woocommerce_social_login_registration'] = $input['show_woocommerce_social_login_registration'];
    
    // roi integrations
    $options['disable_woocommerce_roi'] = $input['disable_woocommerce_roi'];
    $options['disable_eshop_roi'] = $input['disable_eshop_roi'];
    
    // Purchase Sharing settings
    if (!$input['purchase_sharing']) $input['purchase_sharing'] = 0;
    $options['purchase_sharing'] = $input['purchase_sharing'];
    $options['purchase_sharing_header'] = $input['purchase_sharing_header'];
    $options['purchase_sharing_image'] = $input['purchase_sharing_image'];
    $options['purchase_sharing_url'] = $input['purchase_sharing_url'];
    $options['purchase_sharing_name'] = $input['purchase_sharing_name'];
    $options['purchase_sharing_description'] = $input['purchase_sharing_description'];
    
    add_settings_error(
        'shop_pe_options',
        'settings_updated',
        $message,
        $type
    );

    return $options;
}
endif;

// check if WooCommerce is installed
function woocommerce_is_installed() {
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return true;
	else return false;
}

// check if eShop is installed
function eshop_is_installed() {
	if ( has_action( 'eshop_on_success' ) ) return true;
	else return false;
}

// grab the saved options
	$options = get_option( 'shop_pe_options' );

// attach Share for Coupon button fuction to hook on WooCommerce cart page, if enabled
	if ($options['show_coupon_button_woocommerce_cart'] == 1 && !empty($options['shop_id']) ){
		add_action( 'woocommerce_cart_coupon', 'addshoppers_coupon_button' );
	}

function addshoppers_coupon_button() {
	echo '<br /><div class="share-buttons share-buttons-panel" data-style="coupon" style="margin-top: 5px;"></div>';
}


// attach WooCommerce social login action to hook, if enabled
	if ($options['show_woocommerce_social_login'] == 1 && !empty($options['login_networks']) && !empty($options['shop_id']) && !empty($options['api_secret'])):
		add_action( 'woocommerce_before_customer_login_form', 'addshoppers_woocommerce_social_login' );
	endif;

function addshoppers_woocommerce_social_login() {
	$options = get_option( 'shop_pe_options' );
	addshoppers_show_social_login(implode($options['login_networks'],','),'medium');
}

/*
Social Login hooks that will be released in a later WooCommerce version

// show social login buttons in WooCommerce login form if enabled
if ( has_action( 'woocommerce_login_form_start' ) ):
	$options = get_option( 'shop_pe_options' );
	if ($options['show_woocommerce_social_login_registration'] == 1 && !empty($options['shop_id']) && !empty($options['api_secret'])):
		add_action( 'woocommerce_login_form_start', 'addshoppers_woocommerce_social_login' );
	endif;
endif;

// show social login buttons in WooCommerce registration form if enabled
if ( has_action( 'woocommerce_register_form_start' ) ):
	$options = get_option( 'shop_pe_options' );
	if ($options['show_woocommerce_social_login_login'] == 1 && !empty($options['shop_id']) && !empty($options['api_secret'])):
		add_action( 'woocommerce_register_form_start', 'addshoppers_woocommerce_social_login' );
	endif;
endif;
*/


// install ROI tracking in WooCommerce if installed
if (woocommerce_is_installed()):
	$options = get_option( 'shop_pe_options' );
	if ($options['disable_woocommerce_roi'] != 1 && !empty($options['shop_id'])):
		add_action( 'woocommerce_thankyou', 'addshoppers_roi_tracking' );
		function addshoppers_roi_tracking( $order_id ) {
   			$order = new WC_Order( $order_id );
   			$options = get_option( 'shop_pe_options' );
   			show_roi_tracking($options['shop_id'], $order_id, $order->get_order_total());
		}
	endif;
endif;

// install Purchase Sharing in WooCommerce if installed
if (woocommerce_is_installed()):
	$options = get_option( 'shop_pe_options' );
	if ( $options['purchase_sharing'] != 0 ):
		add_action( 'woocommerce_thankyou', 'addshoppers_purchase_sharing_woocommerce' );
	endif;
endif;

// install ROI tracking in eShop if installed
if ( has_action( 'eshop_on_success' ) ):
	$options = get_option( 'shop_pe_options' );
	if ($options['disable_eshop_roi'] != 1 && !empty($options['shop_id'])):
		add_action( 'eshop_on_success', 'addshoppers_roi_tracking' );
		function addshoppers_roi_tracking( $checked ) {
   			$order = eshop_rtn_order_details($checked);
   			$options = get_option( 'shop_pe_options' );
   			show_roi_tracking($options['shop_id'], $order['transid'], $order['total']);
		}
	endif;
endif;

function show_roi_tracking_admin($plugin_name, $options) {
	$lower_name = strtolower($plugin_name);
?>
	<h3 style="margin-top: 0px;"><?php echo $plugin_name; ?></h3>
	<?php if (!empty($options['shop_id'])) { ?>
		<p class="description">We've detected that you're using <?php echo $plugin_name; ?>. We'll automatically integrate our ROI Tracking app into your shop so that you can track social revenue. However, if you'd like to disable this, please check the checkbox belox.</p>
		<p><input id="disable-<?php echo $lower_name; ?>-roi" type="checkbox" name="shop_pe_options[disable_<?php echo $lower_name; ?>_roi]" value="1" <?php if ($options['disable_' . $lower_name . '_roi'] == 1 ) echo 'checked="checked" '; ?>/> Disable ROI Tracking.</p>
		<?php } else { ?>
			<p class="description">We've detected that you're using <?php echo $plugin_name; ?>, but you don't have an AddShoppers account (or, you do and you haven't put your Shop ID in the correct box above). Follow the instructions in the Shop ID box to connect your <?php echo $plugin_name; ?> shop to AddShoppers and then we'll automatically start tracking ROI.</p>
		<?php } 
}

function show_roi_tracking($shop_id, $order_id, $order_total) {
	?>
		<script type="text/javascript">
			AddShoppersConversion = {
    	    	order_id: '<?php echo $order_id; ?>',
    	    	value: '<?php echo $order_total; ?>'
   			};
			var js = document.createElement('script'); js.type = 'text/javascript'; js.async = true; js.id = 'AddShoppers';
			js.src = ('https:' == document.location.protocol ? 'https://shop.pe/widget/' : 'http://cdn.shop.pe/widget/') + 'widget_async.js#<?php echo $shop_id; ?>';
			document.getElementsByTagName("head")[0].appendChild(js);
		</script>	
	<?php
}

function show_addshopperstracking($auto, $sharing_data) {
	$sharing_data['auto'] = $auto;
	?>
	<script type="text/javascript">
	AddShoppersTracking = <?php echo json_encode($sharing_data); ?>;
	</script>
	<?php
}

function addshoppers_purchase_sharing_woocommerce( $order_id ) {
	$order = new WC_Order( $order_id );
	$options = get_option( 'shop_pe_options' );
	
	$product_data = array('header' => $options['purchase_sharing_header']);
		
	if ( sizeof( $order->get_items() ) == 1 ) {
		foreach ( $order->get_items() as $cart_item_key => $cart_item ) {
			$_pf = new WC_Product_Factory(); 
			$_product = $_pf->get_product($cart_item['product_id']);
			$product_data['price'] = get_woocommerce_currency_symbol() . $_product->get_price();	
			$product_data['description'] = $_product->post->post_content;
			$product_data['name']= $_product->post->post_title;
			$product_data['url'] = get_permalink( $cart_item['product_id'] );
			$product_data['image'] = wp_get_attachment_url( get_post_thumbnail_id( $cart_item['product_id'] ) );	
		}
	}
	else {
			$product_data['description'] = $options['purchase_sharing_description'];
			$product_data['name']= $options['purchase_sharing_name'];
			$product_data['url'] = $options['purchase_sharing_url'];
			$product_data['image'] = $options['purchase_sharing_image'];	
	}
	show_addshopperstracking(true,$product_data); 
}

if (woocommerce_is_installed()):
	add_action( 'woocommerce_cart_contents', 'addshoppers_cart_page_info' );
endif;

function addshoppers_cart_page_info() {
	global $woocommerce;	
	$product_data = array();
	foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {
		$_pf = new WC_Product_Factory(); 
		$_product = $_pf->get_product($cart_item['product_id']);
		$product_data['price'] = get_woocommerce_currency_symbol() . $_product->get_price();	
		$product_data['description'] = $_product->post->post_content;
		$product_data['name'] = $_product->post->post_title;
		$product_data['url'] = get_permalink( $cart_item['product_id'] );
		$product_data['image'] = wp_get_attachment_url( get_post_thumbnail_id( $cart_item['product_id'] ) );	
		show_addshopperstracking(false,$product_data); 
	}
}