jQuery(document).ready(function(){

	var cta_shop_id = setting_obj.btn_settings.cta_shop_id;
	var container_id = setting_obj.btn_settings.container_id;
	var cta_language = setting_obj.btn_settings.cta_language;
	var cart_btn = setting_obj.btn_settings.show_add_to_cart_btn;
	var enable_demo = setting_obj.btn_settings.enable_demo;
	var attributes_size = setting_obj.btn_settings.attributes_size;
	//var brand_manufacturer = setting_obj.btn_settings.brand_manufacturer;
	var manufacturer = setting_obj.btn_settings.solo_manufacturer;

	var product_title = setting_obj.product.product_title;
	var product_cat = setting_obj.product.product_cat;
	var product_brand = setting_obj.product.product_brand;
	var ajax_url = setting_obj.ajax_url;

	if (cta_shop_id == '') {
		cta_shop_id = 9999999;
	}
	if (container_id == '') {
		container_id = 'fashionfitr-after-sku';
	}
	if (cta_language == '') {
		cta_language = 'en-EN';
	}
	if (cart_btn == 'yes') {
		cart_btn = true;
	} else {
		cart_btn = false;
	}
	if (enable_demo == 'yes') {
		enable_demo = true;
	} else {
		enable_demo = false;
	}
	
	if (manufacturer == '') {
		manufacturer = product_brand;
	}
	console.log(manufacturer);
	fashionFitrButton.getButton({
					shopid: cta_shop_id,
                    module: product_cat, //'T-Shirts',
                    gender: 'w',
                    brand: manufacturer,
                    label: 'size',                 //'label' or 'size', or 'combi' e.g. XXL / 40 / XXL-40
                    language: cta_language,
                    container: container_id,
                    name: product_title,
                    addToCart: cart_btn,
                    measurement: '34',
                    fakebutton:enable_demo,               // for testmodus development only
                    callback: function(item){
                    	console.log(item);
                    	var pq = jQuery('.input-text.qty').val();
                    	if(pq < 0) {
                    		pq = 1;
                    	}
						var data = {
								'action': 'cta_add_product_to_cart',
								'size' : item.size_selected,
								'pid' : setting_obj.product.product_id,
								'pq' : pq
						};
						jQuery.post(ajax_url, data, function(response){
							var msg = '<div class="woocommerce-message" role="alert">' + response + '</div>';
							jQuery('.site-main').find('.woocommerce-message').remove();
							jQuery('.site-main').prepend(msg);
							
							jQuery('html, body').animate({
						    scrollTop: jQuery('.woocommerce-message').offset().top - 100
						}, 1000);

						});
                    }
               });
});