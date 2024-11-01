<?php
/*
Plugin Name: Woo Direct Order Gateway
Version: 1.0.0
Plugin URI: http://jomarliponko.com/portfolio/wc-pay-later-payment
Description: Woo Direct Order Gateway will disable all current payment gateway in order to proceed to checkout without payment. 
Author: Jomar Lipon
Author URI: http://www.jomarliponko.com
License: GPL v3
*/


if(!class_exists('Woo_Direct_Order_Gateway')) {

	class Woo_Direct_Order_Gateway {
		public function __construct() 
		{
			//wc default hook to disable payment functionality on checkout.
			add_filter('woocommerce_cart_needs_payment', '__return_false');
			add_filter('woocommerce_review_order_before_payment', array($this,'wdog_add_text_for_payment'),10);
			add_filter('woocommerce_thankyou', array($this,'wdog_thank_you_page_order_on_hold'),20,1 );
			add_filter('woocommerce_thankyou_order_received_text', array($this,'wdog_thank_you_text'),10,2 );


		}

		public function wdog_add_text_for_payment() {
			echo '<h5><strong>Payment later is default to the site. Please check your inbox for the payment of your order.</strong></h5>';
		}
		public function wdog_thank_you_page_order_on_hold($order_id) {
			$order = new WC_Order( $order_id );
			$order_pay_method = get_post_meta( $order->id, '_payment_method', true );
			if($order_pay_method == ''){
				$order->update_status( 'pending');
			}
			else {
				$order->update_status( 'processing');
			}
		}
		public function wdog_thank_you_text($paymenttext, $order_id) {
			$order = new WC_Order( $order_id );
			$order_pay_method = get_post_meta( $order->id, '_payment_method', true );
			if($order_pay_method == ''){
				$paymenttext = '<p><strong>Order received. Please check your inbox for your customer invoice including shipping fee within a day.</strong></p>';
			}
			else {
				$paymenttext = '<p>Payment received. We will process your order.</p>';
			}
			return $paymenttext;
		}

	}

	$wdog = new Woo_Direct_Order_Gateway();
}
?>