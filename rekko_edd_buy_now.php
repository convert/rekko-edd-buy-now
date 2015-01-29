<?php
/* 
 * Plugin Name: Rekko EDD Buy Now
 * Plugin URL: http://github.com/convert/rekko-edd-buy-now
 * Description: When using the Buy Now option, associate the transaction with the user if possible.
 * Version: 0.1
 * Author: Ghais Issa
 * Author URI: email://issa@rekko.com
 */

class Rekko_EDD_Buy_Now {

  private static $instance;

  /**
   * Get active object instance.
   *
   * @since 0.1
  
   * @access public
   * @static
   * @return object
   */
  public static function get_instance() {
    if (!self::$instance) {
      self::$instance = new Rekko_EDD_Buy_Now();
    }

    return self::$instance;
  }

  /**
   * Class constructor.  includes and init method. 
   *
   * @since 0.1
   *
   * @access public
   * @return void
   */
  public function __construct() {
    $this->init();
  }

  /**
   * Run action and filter hooks.
   *
   * @since 0.1
   *
   * @access private
   * @return void
   */
  private function init() {
    if(!function_exists('edd_price') ) {
      return; // EDD not present
    }
    add_action('edd_complete_purchase', array($this, 'rekko_complete_purchase'));
  }
  
  /*
   * Process a standard payment and associated guest users with any records we
   * have for them.
   * 
   * @since 0.1
   * @access public
   * @static
   * @return void
   */
  public static function rekko_complete_purchase($payment_id) {
    $user = edd_get_payment_meta_user_info($payment_id);
    if ($user["id"] > 0) {
      //logged in user, do nothing.
      return;
    }
    $user = get_user_by('email', $user["email"]);
    if ($user) {
      edd_update_payment_meta($payment_id, '_edd_payment_user_id' , $user->ID);
      return;
    }
    //the user is not in the system. So do nothing.
  }
}

$GLOBALS['rekko_edd_buy_now'] = new Rekko_EDD_Buy_Now();
