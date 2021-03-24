<?php
/**
 * WooTicketBooking settings
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class CTA_Settings {

	public function __construct() {

		add_action('admin_menu',array($this, 'add_setting_nav'));
		add_action('wp_enqueue_scripts', array($this, 'cta_setting_style_and_script'));
		add_action('init', array($this, 'save_all_setting'));
		add_action('woocommerce_after_add_to_cart_button', array($this, 'fashionfitr_btn'));
		add_action('woocommerce_product_meta_end', array($this, 'fashionfitr_btn_after_sku'));
		add_action('wp_ajax_cta_add_product_to_cart', array($this, 'cta_add_product_to_cart'));
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		
		//add_action( 'woocommerce_add_cart_item_data', array( $this, 'store_custom_field_to_cart'), 10, 2 );
		//add_action( 'woocommerce_add_order_item_meta', array( $this, 'store_custom_field_to_order_meta'), 10, 3 );
		//add_filter('woocommerce_get_item_data', array( $this, 'rendering_meta_field_on_cart_and_checkout'), 10, 2);
		//add_action( 'woocommerce_after_order_itemmeta', array( $this, 'get_custom_field_to_order_meta'),10, 3 );
	}

	public function cta_setting_style_and_script() {

		wp_enqueue_script( 'cta-fashionfitr-script', 'https://popup.fashionfitr.com/js/ffb-min.js');
		wp_enqueue_style( 'cta-fashionfitr-style', 'https://popup.fashionfitr.com/js/fashion-fitr-button.css');
		wp_enqueue_style( 'cta-style', CTA_PLUGIN_URL . '/css/style.css');
        wp_enqueue_script( 'cta-setting-script', CTA_PLUGIN_URL . '/js/cta-setting.js', array( 'jquery' ), CTA_VERSION, true );

        $arr_option = json_decode(get_option('cta_settings', true));
        $arrProduct = $this->get_current_product();

        $arrBtn = array(
        				'btn_settings' => $arr_option,
        				'product' => $arrProduct,
        				'ajax_url' => admin_url('admin-ajax.php')
        			);

        wp_localize_script('cta-setting-script', 'setting_obj', $arrBtn);
	}

	public function add_setting_nav() {

		add_menu_page('CTA Setting', 'CTA Setting', 'manage_options', 'cta_setting', array($this, 'cta_setting_content'), '', 90);
 	}

	public function cta_setting_content() {

		include CTA_ABSPATH . 'view/html-cta-settings.php';
	}

	public function add_fields() {

		$field = array();
		
		$field[] = array(
			'name' 	=> __('Shop ID*', 'html5reset'),
			'desc' 	=> __('Shop ID.', 'html5reset'),
			'id' 	=> 'cta_shop_id',
			'std' 	=> '',
			'type' 	=> 'text');

		$field[] = array(
			'name' 	=> __('Language', 'html5reset'),
			'desc' 	=> __('Language.', 'html5reset'),
			'id' 	=> 'cta_language',
			'std' 	=> '',
			'option'=> array( 'en-EN' => 'English', 'nl-NL' => 'Dutch'),
			'type' 	=> 'select');

		$field[] = array(
			'name' 	=> __('Solo Manufacturer', 'html5reset'),
			'desc' 	=> __('Solo Manufacturer.', 'html5reset'),
			'id' 	=> 'solo_manufacturer',
			'std' 	=> '',
			'type' 	=> 'text');

		$arrayAttr = wc_get_attribute_taxonomies();
		$attr = array('' => 'Select Attribute');
		if (!empty($arrayAttr)) {
			foreach ($arrayAttr as $attribute) {
				$attr[$attribute->attribute_id] = $attribute->attribute_name;
			}
		}

		$field[] = array(
			'name' 	=> __('Brand Atttribute', 'html5reset'),
			'desc' 	=> __('Brand Atttribute', 'html5reset'),
			'id' 	=> 'brand_manufacturer',
			'std' 	=> '',
			'option'=> $attr,
			'type' 	=> 'select');

		$field[] = array(
			'name' 	=> __('Size Atttribute', 'html5reset'),
			'desc' 	=> __('Size Atttribute', 'html5reset'),
			'id' 	=> 'size_attributes',
			'std' 	=> '',
			'option'=> $attr,
			'type' 	=> 'select');

		/*$arrayAttr = wc_get_attribute_taxonomies();
		$attr = array('' => 'Select brand');
		if (!empty($arrayAttr)) {
			foreach ($arrayAttr as $attribute) {
				if ($attribute->attribute_name == 'brand') {
					$terms = get_terms('pa_'.$attribute->attribute_name, array(
									    'hide_empty' => false,
									));
					
					if (!empty($terms)) {
						
						foreach ($terms as $term) {
							$attr[$term->name] = $term->name;
						}
					}
				}
			}
		}*/

		/*$field[] = array(
			'name' 	=> __('Size label', 'html5reset'),
			'desc' 	=> __('Size label.', 'html5reset'),
			'id' 	=> 'attributes_size',
			'std' 	=> '',
			'option'=> array( 'label' => 'Label', 'size' => 'Size', 'combi' => 'Combine'),
			'type' 	=> 'select');
*/
		$field[] = array(
			'name' 	=> __('Container ID', 'html5reset'),
			'desc' 	=> __('Container ID/Class to put the FashionFitr Button.', 'html5reset'),
			'id' 	=> 'container_id',
			'std' 	=> '',
			'type' 	=> 'text');

		$field[] = array(
			'name' 	=> __('API username', 'html5reset'),
			'desc' 	=> __('API username.', 'html5reset'),
			'id' 	=> 'api_username',
			'std' 	=> '',
			'type' 	=> 'text');

		$field[] = array(
			'name' 	=> __('API password', 'html5reset'),
			'desc' 	=> __('API Password.', 'html5reset'),
			'id' 	=> 'api_password',
			'std' 	=> '',
			'type' 	=> 'text');

		$field[] = array(
			'name' 	=> __('Show Add to cart button?', 'html5reset'),
			'desc' 	=> __('Show Add to cart button.', 'html5reset'),
			'id' 	=> 'show_add_to_cart_btn',
			'std' 	=> '',
			'option'=> array( 'yes' => 'True (AddToCart button)', 'no' => 'False (Close button)'),
			'type' 	=> 'select');

		$field[] = array(
			'name' 	=> __('Enable Demo?', 'html5reset'),
			'desc' 	=> __('Enable Demo.', 'html5reset'),
			'id' 	=> 'enable_demo',
			'std' 	=> '',
			'option'=> array( 'yes' => 'Yes', 'no' => 'No'),
			'type' 	=> 'select');

		return $field;
	}

	public function prepare_fields() {

		$fields = $this->add_fields();
		$html = '';
		$html .= '<table class="form-table">';
			$html .= '<tbody>';
				
				$arr_option = json_decode(get_option('cta_settings', true));
				
				foreach ($fields as $id => $field) {
					
					$id = $field['id']; 
					if($arr_option->$id != '') {

						$value = $arr_option->$id;
					} else {

						$value = '';
					}

					switch ($field['type']) {

						case "colorpicker":

							$html .= '<tr valign="top">
											<th scope="row" class="titledesc">
												<label for="'.$field['id'].'">'.$field['name'].'</label>
												<span class="woocommerce-help-tip"></span>
											</th>
											<td class="form-colorpicker">
												<input name="'.$field['id'].'" id="'.$field['id'].'" type="text" style="" value="'.$value.'" class="" placeholder="'.$field['name'].'">
											</td>
										</tr>';
							break;

						case "select":

							$option = '';
							
							if(!empty($field['option'])) {

								$selected = '';
								foreach ($field['option'] as $id => $optionval) {

									if($id == $value) {

										$option .= '<option value="'.$id.'" selected="selected">'.$optionval.'</option>';
									} else {

										$option .= '<option value="'.$id.'">'.$optionval.'</option>';
									}
								}
							} else {

								$option .= '<option value="Yes">Yes</option>';
							}
							$html .= '<tr valign="top">
											<th scope="row" class="titledesc">
												<label for="'.$field['id'].'">'.$field['name'].'</label>
												<span class="woocommerce-help-tip"></span>
											</th>
											<td class="form-colorpicker">
												<select name="'.$field['id'].'" id="'.$field['id'].'" placeholder="'.$field['name'].'">'.$option.'</select>
											</td>
										</tr>';
							break;

						default:

							$html .= '<tr valign="top">
											<th scope="row" class="titledesc">
												<label for="'.$field['id'].'">'.$field['name'].'</label>
												<span class="woocommerce-help-tip"></span>
											</th>
											<td class="form-colorpicker">
												<input name="'.$field['id'].'" id="'.$field['id'].'" type="text" style="" value="'.$value.'" class="" placeholder="'.$field['name'].'">
											</td>
										</tr>';
							break;
					}
				}
			$html .= '</tbody>';
			$html .= '</tfoot>';
				$html .= '</tr>';
					$html .= '<td></td>';
					$html .= '<td><p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p></td>';
				$html .= '</tr>';
			$html .= '</tfoot>';
		$html .= '</table>';
		return $html;
	}

	public function save_all_setting() {
		
		if (isset($_POST['cta_shop_id']) && $_POST['cta_shop_id'] == '' || isset($_POST['brand_manufacturer']) && $_POST['brand_manufacturer'] == '') {
			add_action( 'admin_notices', array($this, 'cta_err_msg' ));	
			return;
		}

		if (isset( $_POST['_cta_settings'] ) && wp_verify_nonce( $_POST['_cta_settings'],'save_cta_settings' )){
			update_option('cta_settings', json_encode($_POST));
			add_action( 'admin_notices', array($this, 'cta_update_setting' ));
		}
	}

	public function cta_update_setting() {
	    ?>
	    <div class="updated notice">
	        <p><?php _e( 'Settings are saved successfully.', 'cta' ); ?></p>
	    </div>
	    <?php
	}

	public function cta_err_msg() {
	    ?>
	    <div class="error notice">
	        <p><?php _e( 'Plese enter value in required field', 'cta' ); ?></p>
	    </div>
	    <?php
	}
	
	public function fashionfitr_btn() {

		echo '<div id="fashionfitr-btn"></div>';
	}

	public function fashionfitr_btn_after_sku() {

		echo '<div id="fashionfitr-after-sku"></div>';
	}

	public function get_current_product() {

		$arrProduct = array();
		if ( is_product() && is_single() ){
			$arrProduct['product_title'] = get_the_title(get_the_id());
			$terms = get_the_terms ( get_the_id(), 'product_cat' );
			$i = 1;
			$cat_name = '';
			foreach ( $terms as $term ) {
				$cat_name .= $term->name;
				if ($i > 1) {
					$cat_name .= ", ";
				}
				$i++;
			}
			$arrProduct['product_cat'] = $cat_name;
			$arrProduct['product_id'] = get_the_id();

			$arrSettings = json_decode(get_option('cta_settings', true));
			
			if (isset($arrSettings->brand_manufacturer) && $arrSettings->brand_manufacturer != '') {
				$attribute_slug = wc_attribute_taxonomy_name_by_id($arrSettings->brand_manufacturer);
				$brand_term = get_the_terms( get_the_id(), $attribute_slug);
				$j = 1;
				$brand_name = '';
				foreach ( $brand_term as $brand ) {
					  	$brand_name .= $brand->name;
					  	if ($j > 1) {
							$brand_name .= ", ";
						}
						$j++;
				}
				$arrProduct['product_brand'] = $brand_name;
			}
		}

		return $arrProduct;
	}

	public function iconic_get_default_attributes( $product ) {
 
	    if( method_exists( $product, 'get_default_attributes' ) ) {
	 
	        return $product->get_default_attributes();
	 
	    } else {
	 
	        return $product->get_variation_default_attributes();
	 
	    }
	 
	}
	public function cta_add_product_to_cart() {

		
		if (isset($_POST['pid']) && $_POST['pid'] != '') {

			$cart = WC()->instance()->cart;
			$cart_id = $cart->generate_cart_id($_POST['pid']);
		    $cart_item_id = $cart->find_product_in_cart($cart_id);
		    
		    if($cart_item_id){
		       $cart->set_quantity($cart_item_id, 0);
		    }
		    
		    /* Get variation attribute based on product ID */
			$product = new WC_Product_Variable( $_POST['pid'] );
			$variations = $product->get_available_variations();

			$arrSettings = json_decode(get_option('cta_settings', true));
			$pa_size = wc_attribute_taxonomy_name_by_id($arrSettings->size_attributes);
			$var_data = [];
			$key = 'attribute_'.$pa_size;
			foreach ($variations as $variation) {
				if (isset($variation['attributes'][$key])) {
					$var_data[$variation['variation_id']] = $variation['attributes'][$key];
				}
			}
			
			$size = $_POST['size'];
			$variation_id = '';
			if (in_array($size, $var_data)) {
				$variation_id = array_search($size, $var_data);
			}
			if ($variation_id != '') {
				
				// if no products in cart, add it
				$cart = WC()->cart->add_to_cart( $_POST['pid'], $_POST['pq'], $variation_id, array($size) );
				
				echo wc_add_to_cart_message( $_POST['pid'], true, true );	
			} else {
				add_action('woocommerce_before_single_product', array($this, 'error_msg_add_cart'));
			}
		}
		die();
	}

	/**
	 * Store custom field in Cart
	 */
	public function store_custom_field_to_cart( $cart_item_data, $product_id ) {

	    if( isset($_POST['size']) && $_POST['size'] != '') {
	        $cart_item_data[ 'Size' ] = $_POST['size'];
	    }
	    
	    return $cart_item_data;
	}

	/**
	 * Render meta on cart and checkout
	 */
	public function rendering_meta_field_on_cart_and_checkout( $cart_data, $cart_item ) {
	    
	    if(isset($cart_item['Size']) && $cart_item['Size'] != '') {
			$custom_items[] = array( "name" => __( 'Size', "wtb" ), "value" => $cart_item['Size'] );
		}
	    return $custom_items;
	}

	public function store_custom_field_to_order_meta($item_id, $values, $cart_item_key) {

		if(!empty($values['Size'])) {
			wc_add_order_item_meta($item_id, 'Size', $values['Size'], true);
		}
	}

	public function get_custom_field_to_order_meta($item_id, $item, $product) {

		$cta_size = wc_get_order_item_meta( $item_id, 'cta_size', true);
		?>
		 
		<?php if (!empty($cta_size)) {?>
			<div class="order_data_column" style="float: left; width: 50%; padding: 0 5px;">
			<h4><?php _e( 'Size' ); ?></h4>
				<?php 
				echo '<p><span style="display:inline-block; width:100px;">test</span><span>:&nbsp;123</span></p>';
				?>
			</div>
		<?php }
	}

	public function error_msg_add_cart() {
		echo '<div class="woocommerce-message error" role="alert">Product cannot be added to the cart.</div>';
	}
}
new CTA_Settings;