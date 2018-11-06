<?php
/*
 * Plugin Name: Try at Home
 * Version: 1.0.0
 * Description: 
 * Author: Ashutosh Gangwar
 * Author URI: http://ashutoshgngwr.github.io
 * Plugin URI: 
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0
*/

function try_at_home_register_settings() {
  register_setting( 'try_at_home_options_group', 'try_at_home_charges', 'try_at_home_callback' );
  register_setting( 'try_at_home_options_group', 'try_at_home_enable_bundle', 'try_at_home_callback' );
  register_setting( 'try_at_home_options_group', 'try_at_home_bundle_size', 'try_at_home_callback' );
  register_setting( 'try_at_home_options_group', 'try_at_home_fee_label', 'try_at_home_callback');
}

add_action( 'admin_init', 'try_at_home_register_settings' );

function register_try_at_home_submenu_page() {
  add_submenu_page( 'woocommerce', 'Try at Home Settings', 'Try at Home Settings',
    'manage_options', 'try-at-home', 'try_at_home_options_page' );
}

add_action( 'admin_menu', 'register_try_at_home_submenu_page' );

function try_at_home_options_page() {
?>
  <div>
  <?php screen_icon(); ?>
  <h2>Try At Home Settings</h2>
  <form method="post" action="options.php">
  <?php settings_fields( 'try_at_home_options_group' ); ?>
  <table>
  <tr valign="top">
  <th scope="row"><label for="try_at_home_enable_bundle">Apply charges on</label></th>
  <td>
    <label for="per_product">
      <input type="radio" name="try_at_home_enable_bundle" value="0"
        <?php if(get_option('try_at_home_enable_bundle', 1) != '1') echo "checked"; ?>>
        individual product
    </label></br>
    <label for="bundle_product">
      <input type="radio" name="try_at_home_enable_bundle" value="1"
        <?php if(get_option('try_at_home_enable_bundle', 1) == '1') echo "checked='checked'"; ?>>
        bundle of products
    </label>
  </tr>
  <tr valign="top">
  <th scope="row"><label for="try_at_home_bundle_size">Items in a bundle</label></th>
  <td>
    <input type="number" id="try_at_home_bundle_size" name="try_at_home_bundle_size"
      value="<?php echo get_option('try_at_home_bundle_size', 5); ?>" /></td>
  </tr>
  <tr valign="top">
  <th scope="row"><label for="try_at_home_charges">Try at home charges</label></th>
  <td>
    <input type="number" id="try_at_home_charges" name="try_at_home_charges"
      value="<?php echo get_option('try_at_home_charges', 300); ?>" /></td>
  </tr>
  <tr valign="top">
  <th scope="row"><label for="try_at_home_fee_label">Label of charges</label></th>
  <td>
    <input type="text" id="try_at_home_fee_label" name="try_at_home_fee_label"
      value="<?php echo get_option('try_at_home_fee_label', 'Try at Home charges'); ?>" /></td>
  </tr>
  </table>
  <?php  submit_button(); ?>
  </form>
  </div>
<?php
}

function prefix_add_try_at_home_charges( $cart ) {
  $try_at_home_charges = get_option('try_at_home_charges', 300);
  $bundle_size = get_option('try_at_home_bundle_size', 5);
  $fee_label = get_option('try_at_home_fee_label', 'Try at Home charges');
  if (get_option('try_at_home_enable_bundle', 1) == '1') {
    $cart_fee = ($try_at_home_charges * ((int) ($cart->get_cart_contents_count() / $bundle_size ) + 1)) - $cart->get_subtotal();
  } else {
    $cart_fee = ($try_at_home_charges * $cart->get_cart_contents_count()) - $cart->get_subtotal();
  }
  $cart->add_fee( __( $fee_label, 'try-at-home-charges' ) , $cart_fee, true );
}

add_action( 'woocommerce_cart_calculate_fees', 'prefix_add_try_at_home_charges' , 10 );