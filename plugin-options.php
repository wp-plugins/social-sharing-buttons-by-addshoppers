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

/**
 * Include admin CSS only the plugin settings page.
 *
 * @since WPShopPe 1.0
 */
function my_enqueue($hook) {
    if( 'settings_page_shop-pe-plugin' != $hook )
        return;
    wp_enqueue_style( 'addshoppers_admin_css', AS_PLUGIN_FOLDER . 'addshoppers-admin.css' );
}
add_action( 'admin_enqueue_scripts', 'my_enqueue' );


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
	<iframe id="dashboard_iframe" src="https://www.addshoppers.com/merchants" style="border: 0px; margin-top: 10px;<?php if (empty($shop_id)) echo 'display: none;'; ?>" height="600px" width="100%;" />
	<?php
	}
}

function show_settings_form($options) {
    $all_networks = addshoppers_networks('all');
    if (!$options['selected_networks']) $options['selected_networks'] = addshoppers_networks('default'); 
?>
        <form method="post" action="options.php" class="settings">
            <?php settings_fields( 'shop_pe_plugin_options' ); ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">Shop ID</th>
                        <td>
                            <input id="shop-id" class="regular-text" type="text" name="shop_pe_options[shop_id]" value="<?php echo( $options['shop_id'] ); ?>" placeholder="Your ID" />
                            <p class="description">(Optional) Enter your shop ID if you want to track the analytics of your sharing buttons. <br/>You can get your shop ID or sign up for one <a href="https://www.addshoppers.com/merchants">here</a>. Go to &rarr; Settings &rarr; Shops and copy the Shop ID for your shop into the field above.</p>
                        </td>
                     </tr>
                      <tr>
                        <th scope="row">Show Floating Buttons</th>
                        <td>
                            <input id="default-buttons" type="checkbox" name="shop_pe_options[default_buttons]" value="1" <?php if ($options['default_buttons'] == 1 ) echo 'checked="checked" '; ?>/>
                            <p class="description">Check this box to show the default floating buttons. If you want different buttons, grab the code for the buttons you want in your <a href="https://www.addshoppers.com/merchants">AddShoppers Dashboard</a> under Apps &rarr; Sharing Buttons. Copy and paste the code for your buttons into the desired location in your active theme.</p>
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
                    <tr>
                        <th scope="row">ROI Tracking</th>
                        <td>
                        	<?php 
                        	// if WooCommerce is detected
                        	if (!woocommerce_is_installed()) {
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
	
	// default buttons
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
    
    // roi integrations
    $options['disable_woocommerce_roi'] = $input['disable_woocommerce_roi'];
    $options['disable_eshop_roi'] = $input['disable_eshop_roi'];
    
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
	if ( function_exists( 'woocommerce_thankyou' ) ) return true;
	else return false;
}

// check if eShop is installed
function eshop_is_installed() {
	if ( function_exists( 'eshop_on_success' ) ) return true;
	else return false;
}

// install ROI tracking in WooCommerce if installed
if ( function_exists( 'woocommerce_thankyou' ) ):
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

// install ROI tracking in eShop if installed
if ( function_exists( 'eshop_on_success' ) ):
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
    	    	order_id: <?php echo $order_id; ?>,
    	    	value: <?php echo $order_total; ?>
   			};
			var js = document.createElement('script'); js.type = 'text/javascript'; js.async = true; js.id = 'AddShoppers';
			js.src = ('https:' == document.location.protocol ? 'https://shop.pe/widget/' : 'http://cdn.shop.pe/widget/') + 'widget_async.js#<?php echo $shop_id; ?>';
			document.getElementsByTagName("head")[0].appendChild(js);
		</script>	
	<?php
}