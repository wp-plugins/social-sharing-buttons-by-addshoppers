<?php
/**
 * AddShoppers Plugin
 *
 * @package WPShopPe
 * @version 1.6
 */
/*
Plugin Name: Social Sharing Buttons By AddShoppers
Plugin URI: http://www.addshoppers.com/
Description: Add smart sharing buttons and advanced social analytics with the AddShoppers plugin for WordPress. More social apps are also available at <a href="http://www.addshoppers.com/">http://www.addshoppers.com/</a>.
Author: AddShoppers
Version: 1.6
Author URI: http://www.addshoppers.com/
*/

define("AS_WP_ORG_PLUGIN_SLUG", "social-sharing-buttons-by-addshoppers");
define("AS_DEFAULT_SHOP_ID", "528db56da387646ecd552524");
define("AS_PLUGIN_FOLDER", plugin_dir_url( __FILE__ ));
define("AS_PLUGIN_IMG_FOLDER", plugin_dir_url( __FILE__ ) . "images/");

if ( ! function_exists( 'shop_pe_plugin_wp_footer' ) ):
/**
 * Outputs AddShoppers footer code, if Shop ID is found.
 *
 * @since WPShopPe 1.0
 */
function shop_pe_plugin_wp_footer() {
    $options = get_option( 'shop_pe_options' );

    if ( $options['default_buttons'] != 0 ) {
    
    	if ( empty( $options['shop_id']) )  { $options['shop_id'] = AS_DEFAULT_SHOP_ID; }
    	
    	if (!$options['selected_networks']) { $buttons = 'facebook,twitter,google,email'; }
    	else { $buttons = implode($options['selected_networks'],','); }
	?>
		<!-- AddShoppers Default Floating Buttons -->
		<div class="share-buttons share-buttons-tab" data-buttons="<?php echo $buttons; ?>" data-style="medium" data-counter="true" data-hover="true" data-promo-callout="true" data-float="left"></div>
	<?php } ?>
	
	<!-- AddShoppers.com Social Analytics --> 
	<script type="text/javascript">
	if(typeof AddShoppersTracking === 'undefined'){
    	AddShoppersTracking = {
            lang: { 
            	widget: '<?php echo get_addshoppers_lang(); ?>'
        	}
        };
	}
	else {
		AddShoppersTracking.lang = { widget: '<?php echo get_addshoppers_lang(); ?>' } };
	
	var js = document.createElement('script'); js.type = 'text/javascript'; js.async = true; js.id = 'AddShoppers';
	js.src = ('https:' == document.location.protocol ? 'https://shop.pe/widget/' : 'http://cdn.shop.pe/widget/') + "widget_async.js#<?php echo( $options['shop_id'] ); ?>"; 
	document.getElementsByTagName("head")[0].appendChild(js);
	</script>
    <?php
    
}
endif;
add_action( 'wp_footer', 'shop_pe_plugin_wp_footer' );

// Add settings link on plugin page
function addshoppers_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=shop-pe-plugin">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'addshoppers_settings_link' );


// Additional links on the plugin page (description)
function addshoppers_settings_metalinks($links, $file) {
	$plugin = plugin_basename(__FILE__); 
	if ($file == $plugin) {
		$links[] = '<a href="options-general.php?page=shop-pe-plugin">' . __('Settings','addshoppers') . '</a>';
		$links[] = '<a href="http://wordpress.org/support/view/plugin-reviews/' . AS_WP_ORG_PLUGIN_SLUG . '" target="_blank">' . __('Review our plugin!','addshoppers') . '</a>';
	}
	return $links;
}
add_filter('plugin_row_meta', 'addshoppers_settings_metalinks',10,2);

function get_addshoppers_lang() {
	$locale = strtolower(get_locale());   
	if ($locale == "es_es") $locale = "es_mx";
    $lang = substr( $locale, 0, -2 ) . strtoupper(substr( $locale,-2));
    return $lang;
}

require_once( dirname( __FILE__ ) . '/plugin-options.php' );
require_once( dirname( __FILE__ ) . '/social-login.php' );