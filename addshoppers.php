<?php
/**
 * AddShoppers Plugin
 *
 * @package WPShopPe
 * @version 1.1
 */
/*
Plugin Name: AddShoppers
Plugin URI: http://www.addshoppers.com/
Description: Add smart sharing buttons and advanced social analytics with the AddShoppers plugin for WordPress. More social apps are also available at <a href="http://www.addshoppers.com/">http://www.addshoppers.com/</a>.
Author: AddShoppers
Version: 1.1
Author URI: http://www.addshoppers.com/
*/

define("AS_WP_ORG_PLUGIN_SLUG", "addshoppers");
define("AS_DEFAULT_SHOP_ID", "500975935b3a42793000002b");
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

    if ( empty( $options['default_buttons'] ) == false ) {
    
    	if ( empty( $options['shop_id']) )  { $options['shop_id'] = AS_DEFAULT_SHOP_ID; }
    	
    	if (!$options['selected_networks']) { $buttons = 'facebook,twitter,google,email'; }
    	else { $buttons = implode($options['selected_networks'],','); }
	?>
		<!-- AddShoppers Default Floating Buttons -->
		<div class="share-buttons share-buttons-tab" data-buttons="<?php echo $buttons; ?>" data-style="medium" data-counter="true" data-hover="true" data-promo-callout="true" data-float="left"></div>
	<?php } ?>
	
	<!-- AddShoppers.com Social Analytics --> 
	<script type="text/javascript">
	AddShoppersTracking = {image: ''};
	var js = document.createElement('script'); js.type = 'text/javascript'; js.async = true; js.id = 'AddShoppers';
	js.src = ('https:' == document.location.protocol ? 'https://shop.pe/widget/' : 'http://cdn.shop.pe/widget/') + "widget_async.js#<?php echo( $options['shop_id'] ); ?>"; 
	document.getElementsByTagName("head")[0].appendChild(js);
	</script>
    <?php
    
}
endif;
add_action( 'wp_footer', 'shop_pe_plugin_wp_footer' );



require_once( dirname( __FILE__ ) . '/plugin-options.php' );