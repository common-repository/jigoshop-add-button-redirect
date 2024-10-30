<?php
 /**
 * Plugin Name:         Jigoshop Add Redirect Button
 * Plugin URI:          http://www.chriscct7.com
 * Description:         Adds Button/Redirect to Products
 * Author:              Chris Christoff
 * Author URI:          http://www.chriscct7.com
 *
 * Contributors:        chriscct7
 *
 * Version:             4.0
 * Requires at least:   3.5.0
 * Tested up to:        3.6 Beta 3
 *
 * Text Domain:         jarb
 * Domain Path:         /languages/
 *
 * @category            Plugin
 * @copyright           Copyright © 2013 Chris Christoff
 * @author              Chris Christoff
 * @package             JARB
 */
if ( !class_exists( 'Jigoshop_Add_Redirect_Button' ) ) {
	
	class Jigoshop_Add_Redirect_Button {
		function __construct() {
			add_action( 'jigoshop_product_write_panel_tabs', array( $this, 'jigoshop_product_write_panel_tabs' ) );
			add_action( 'product_write_panels', array( $this, 'product_write_panel' ) );
			add_filter( 'jigoshop_process_product_meta', array( $this, 'me_product_save_data' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
			add_action( 'product_write_panels', array( $this, 'input' ) ); 
			add_action( 'wp_enqueue_scripts', array( $this,'jsab_add_my_stylesheet' ));
		}
		function jspgactivation() {
			// checks if the jigoshop plugin is running and disables this plugin if it's not (and displays a message)
			if ( !is_plugin_active( 'jigoshop/jigoshop.php' ) ) {
				deactivate_plugins( plugin_basename( __FILE__ ) );
				wp_die( sprintf( _x( 'The Jigoshop Add Redirect Button plugin requires %s to be activated in order to work. Please activate %s first.', 'A link to Jigoshop is provided in the placeholders', 'Jigoshop_Add_Redirect_Button' ), '<a href="http://jigoshop.com" target="_blank">Jigoshop</a>', '<a href="http://jigoshop.com" target="_blank">Jigoshop</a>' ) . '<a href="'.admin_url( 'plugins.php' ).'"> <br> &laquo; ' . _x( 'Go Back', 'Activation failed, so go back to the plugins page', 'Jigoshop_Add_Redirect_Button' ) . '</a>' );
			}
			
		}
		function jsab_add_my_stylesheet() {
		global $post;global $_product;
		if (is_singular('product')){
		$button=get_post_meta(get_the_ID(),'button_type',true);
		if (($button == 'productidtocart')||($button == 'productidredirect')||($button == 'url')){
		wp_register_style( 'jsab-style', plugins_url('style.css', __FILE__) ,'jigoshop');
		wp_enqueue_style( 'jsab-style' );
		}
		}
		}
		function jigoshop_product_write_panel_tabs() {
			$terms = get_the_terms( $thepostid, 'product_type' );
			$product_type = ($terms) ? current($terms)->slug : 'simple';
				?>
				<li><a href="#Add_Redirect_Button"><?php _e( 'Add Redirect/Button', 'Jigoshop_Add_Redirect_Button' ); ?></a></li>
				<?php
        }
		function product_write_panel() {
			?>
			<div id="Add_Redirect_Button" class="panel jigoshop_options_panel">
			<?php
				$args = array(
				'id'            => 'button_type',
				'desc'          => 'If you do not want a button, you do not need to do anything. ProductID will add a button, that when clicked, adds the product to cart. URL will add a button that redirects to a URL when clicked.',
				'label'         => __('Select Button Type','jigoshop'),
					'options'      			 => array(
					'none'		    		=> __('No Button','jigoshop'),
					'productidtocart'	    => __('Adds Given Product ID to Cart','jigoshop'),
					'productidredirect'	    => __('Redirects to Given Product ID','jigoshop'),
					'url'	   	   			=> __('URL','jigoshop')
					)
				);
				echo $this->select( $args );
				$args = array(
				'id'            => 'redirection_type',
				'desc'          => 'Select where to redirect to, when add to cart is clicked. If No Redirection is selected, redirection is based off of your Jigoshop Settings.',
				'label'         => __('Select Redirect Type','jigoshop'),
					'options'       => array(
					'none'		    => __('No Redirection','jigoshop'),
					'productid'	    => __('Product ID','jigoshop'),
					'url'	   	    => __('URL','jigoshop')
					)
				);
				echo $this->select( $args );
				$args = array(
					'id'            => 'button_link',
					'desc'          => 'Must have button type set to URL',
					'label'         => __('Button URL (with http://)','Jigoshop_Add_Redirect_Button'),
					'type'          => 'text',
					'placeholder'   => __('','Jigoshop_Add_Redirect_Button'),
				);
				echo $this->input( $args );	
				$args = array(
					'id'            => 'button_text',
					'desc'          => 'Text that will appear on the button',
					'label'         => __('The Button Text','Jigoshop_Add_Redirect_Button'),
					'type'          => 'text',
					'placeholder'   => __('Sample Text','Jigoshop_Add_Redirect_Button'),
				);
				echo $this->input( $args );	
				$args = array(
					'id'            => 'redirect_link',
					'desc'          => 'Must have redirect set to URL',
					'label'         => __('Redirect URL (with http://)','Jigoshop_Add_Redirect_Button'),
					'type'          => 'text',
					'placeholder'   => __('','Jigoshop_Add_Redirect_Button'),
				);
				echo $this->input( $args );	
				$args = array(
					'id'            => 'product_number_button',
					'desc'          => 'Must have button type set to Add by ProductID',
					'label'         => __('Product ID Number for Button','jigoshop'),
					'type'          => 'number',
					'step'          => '1',
					'placeholder'   => __('1','jigoshop'),
				);
				echo $this->input( $args );
				$args = array(
					'id'            => 'product_number_redirect',
					'desc'          => 'Must have redirect set to productID',
					'label'         => __('Product ID Number for Redirect','jigoshop'),
					'type'          => 'number',
					'step'          => '1',
					'placeholder'   => __('1','jigoshop'),
				);
				echo $this->input( $args );
				?>
			</div>
		<?php
		}
		function me_product_save_data($post_id, $post) {
			global $post;
			update_post_meta( $post_id, 'redirect_link', $_POST['redirect_link']);
			update_post_meta( $post_id, 'button_link', $_POST['button_link']);
			update_post_meta( $post_id, 'button_type', $_POST['button_type']);
			update_post_meta( $post_id, 'redirection_type', $_POST['redirection_type']);
			update_post_meta( $post_id, 'product_number_redirect', $_POST['product_number_redirect']);
			update_post_meta( $post_id, 'product_number_button', $_POST['product_number_button']);
			update_post_meta( $post_id, 'button_text', $_POST['button_text']);
		}
		function admin_enqueue($hook) {
			global $post;

			// Don't enqueue script if not on product edit screen
			if ( $hook != 'post.php' || $post->post_type != 'product' )
				return false;
		}
		function select( $field ) {
		global $post;

		$args = array(
			'id'            => null,
			'label'         => null,
			'after_label'   => null,
			'class'         => 'select short',
			'desc'          => false,
			'tip'           => false,
			'multiple'      => false,
			'placeholder'   => '',
			'options'       => array(),
			'selected'      => false
		);
		extract( wp_parse_args( $field, $args ) );

		$selected = ($selected) ? (array)$selected : (array)get_post_meta($post->ID, $id, true);
		$name     = ($multiple) ? $id.'[]' : $id;
		$multiple = ($multiple) ? 'multiple="multiple"' : '';
		$desc     = ($desc)     ? esc_html( $desc ) : false;

		$html = '';

		$html .= "<p class='form-field {$id}_field'>";
		$html .= "<label for='{$id}'>$label{$after_label}</label>";
		$html .= "<select {$multiple} id='{$id}' name='{$name}' class='{$class}' data-placeholder='{$placeholder}'>";

		foreach ( $options as $value => $label ) {
			if ( is_array( $label )) {
				$html .= '<optgroup label="'.esc_attr( $value ).'">';
				foreach ( $label as $opt_value => $opt_label ) {
					$mark = '';
					if ( in_array( $opt_value, $selected ) ) {
						$mark = 'selected="selected"';
					}
					$html .= '<option value="'.esc_attr($opt_value).'"' .$mark.'>'.$opt_label.'</option>';
				}
				$html .= '</optgroup>';
			}
			else {
				$mark = '';
				if ( in_array( $value, $selected ) ) {
					$mark = 'selected="selected"';
				}
				$html .= '<option value="'.esc_attr($value).'"' .$mark.'>'.$label.'</option>';
			}
		}
		$html .= "</select>";

		if ( $tip ) {
			$html .= '<a href="#" tip="'.$tip.'" class="tips" tabindex="99"></a>';
		}

		if ( $desc ) {
			$html .= '<span class="description">'.$desc.'</span>';
		}

		$html .= "</p>";
		$html .=    '<script type="text/javascript">
						jQuery(function() {
							jQuery("#'.$id.'").select2();
						});
					</script>';

		return $html;
	}
		function input( $field ) {
		global $post;
		$args = array(
			'id'            => null,
			'type'          => 'text',
			'label'         => null,
			'after_label'   => null,
			'class'         => 'short',
			'desc'          => false,
			'tip'           => false,
			'value'         => null,
			'min'           => null,
			'max'           => null,
			'step'          => null,
			'placeholder'   => null,
		);
		extract( wp_parse_args( $field, $args ) );
		$value = isset( $value ) ? esc_attr( $value ) : get_post_meta( $post->ID, $id, true) ;
		$html  = '';
		$html .= "<p class='form-field {$id}_field'>";
		$html .= "<label for='{$id}'>$label{$after_label}</label>";
		$html .= "<input type='{$type}' id='{$id}' name='{$id}' class='{$class}'";
		$html .= " value='{$value}'";
		if ( $type == 'number' ) {
			if ( ! empty( $min ))   $html .= " min='{$min}'";
			if ( ! empty( $max ))   $html .= " max='{$max}'";
			if ( ! empty( $step ))  $html .= " step='{$step}'";
		}
		$html .= " placeholder='{$placeholder}' />";
		if ( $desc ) {
			$html .= '<span class="description">'.$desc.'</span>';
		}
		$html .= "</p>";
		return $html;
	}
} // end class

	/**
	 * init the class
	 */
	add_action( 'plugins_loaded', 'Jigoshop_Add_Redirect_Button_init', 1 );
	function Jigoshop_Add_Redirect_Button_init() {

		global $jigoshopaddbutton;
		$jigoshopaddbutton = new Jigoshop_Add_Redirect_Button();
	}
}
if (!function_exists('jigoshop_template_single_add_to_cart')) {
	function jigoshop_template_single_add_to_cart( $post, $_product ) {
		$availability = $_product->get_availability();

		?>
		<p class="stock <?php echo $availability['class'] ?>"><?php echo $availability['availability']; ?></p>
		<?php

		if ( $_product->is_in_stock() ) {
			do_action( $_product->product_type . '_add_to_cart' );
		}

	}
}
if (!function_exists('jigoshop_simple_add_to_cart')) {
	function jigoshop_simple_add_to_cart() {

		global $_product; $availability = $_product->get_availability();

		// do not show "add to cart" button if product's price isn't announced
		if( $_product->get_price() === '') return;
		
		// Type of Button
		$button=get_post_meta($_product->id,'button_type',true);
		$displaybutton=false;
		$type='none';
		$text='Sample Text';
		if ($button == 'productidtocart'){
		$displaybutton=true;
		$text=get_post_meta($_product->id,'button_text',true);
		$producttoadd=get_post_meta($_product->id,'product_number_button',true);
		$type='idcart';
		}
		if ($button == 'productidredirect'){
		$displaybutton=true;
		$text=get_post_meta($_product->id,'button_text',true);
		$productlink=get_post_meta($_product->id,'product_number_button',true);
		$type='idredirect';
		}
		if ($button == 'url'){
		$displaybutton=true;
		$text=get_post_meta($_product->id,'button_text',true);
		$productlink=get_post_meta($_product->id,'button_link',true);
		$type='url';
		}
		?>
		<form action="<?php echo esc_url( $_product->add_to_cart_url() ); ?>" class="cart" method="post">
			<?php do_action('jigoshop_before_add_to_cart_form_button'); ?>
		 	<div class="quantity"><input name="quantity" value="1" size="4" title="Qty" class="input-text qty text" maxlength="12" /></div>
		 	<button type="submit" class="button-alt"><?php _e('Add to cart', 'jigoshop'); ?></button>
			<?php 
		do_action('jigoshop_add_to_cart_form'); ?>
		</form>
		<?php
			if ($displaybutton==true){ 
			if ($type == 'idcart'){
				$displaybutton=true;
				$text=get_post_meta($_product->id,'button_text',true);
				$producttoadd=get_post_meta($_product->id,'product_number_button',true);
				$url = add_query_arg('add-to-cart', $producttoadd);
				$link = jigoshop::nonce_url( 'add_to_cart', $url );
				?>
			<form action="<?php echo $link; ?>" class="jsabbutton" method="post">
			<?php do_action('jigoshop_before_add_to_cart_form_button'); ?>
		 	<input type="hidden" name="quantity" value="1" title="Qty" class="hiddenfield"/>
		 	<button type="submit" class="button-alt"><?php echo $text; ?></button>
			<?php 
			do_action('jigoshop_add_to_cart_form'); ?>
			</form>	
			<?php
			}
			if ($type == 'idredirect'){
				$displaybutton=true;
				$text=get_post_meta($_product->id,'button_text',true);
				$number=get_post_meta($_product->id,'product_number_button',true);
				$productlink=get_permalink( $number);
				?>
				<a href="<?php echo $productlink; ?>" class="button-alt jsabbutton"><?php echo $text; ?></a>
				<?php
			}
			if ($type == 'url'){
				$displaybutton=true;
				$text=get_post_meta($_product->id,'button_text',true);
				$productlink=get_post_meta($_product->id,'button_link',true);
				?>
				<a href="<?php echo $productlink; ?>" class="button"><?php echo $text; ?></a>
				<?php
			}
			}
	}
}
if (!function_exists('jigoshop_downloadable_add_to_cart')) {
	function jigoshop_downloadable_add_to_cart() {

		global $_product; $availability = $_product->get_availability();

		// do not show "add to cart" button if product's price isn't announced
		if( $_product->get_price() === '') return;

			
		// Type of Button
		$button=get_post_meta($_product->id,'button_type',true);
		$displaybutton=false;
		$type='none';
		$text='Sample Text';
		if ($button == 'productidtocart'){
		$displaybutton=true;
		$text=get_post_meta($_product->id,'button_text',true);
		$producttoadd=get_post_meta($_product->id,'product_number_button',true);
		$type='idcart';
		}
		if ($button == 'productidredirect'){
		$displaybutton=true;
		$text=get_post_meta($_product->id,'button_text',true);
		$productlink=get_post_meta($_product->id,'product_number_button',true);
		$type='idredirect';
		}
		if ($button == 'url'){
		$displaybutton=true;
		$text=get_post_meta($_product->id,'button_text',true);
		$productlink=get_post_meta($_product->id,'button_link',true);
		$type='url';
		}

		?>
		<form action="<?php echo esc_url( $_product->add_to_cart_url() ); ?>" class="cart" method="post">
			<?php do_action('jigoshop_before_add_to_cart_form_button'); ?>
			<button type="submit" class="button-alt"><?php _e('Add to cart', 'jigoshop'); ?></button>
			<?php do_action('jigoshop_add_to_cart_form'); ?>
		</form>
		<?php
			if ($displaybutton==true){ 
			if ($type == 'idcart'){
				$displaybutton=true;
				$text=get_post_meta($_product->id,'button_text',true);
				$producttoadd=get_post_meta($_product->id,'product_number_button',true);
				$url = add_query_arg('add-to-cart', $producttoadd);
				$link = jigoshop::nonce_url( 'add_to_cart', $url );
				?>
			<form action="<?php echo $link; ?>" class="jsabbutton" method="post">
			<?php do_action('jigoshop_before_add_to_cart_form_button'); ?>
		 	<input type="hidden" name="quantity" value="1" title="Qty" class="hiddenfield"/>
		 	<button type="submit" class="button-alt"><?php echo $text; ?></button>
			<?php 
			do_action('jigoshop_add_to_cart_form'); ?>
			</form>	
			<?php
			}
			if ($type == 'idredirect'){
				$displaybutton=true;
				$text=get_post_meta($_product->id,'button_text',true);
				$number=get_post_meta($_product->id,'product_number_button',true);
				$productlink=get_permalink( $number);
				?>
				<a href="<?php echo $productlink; ?>" class="button-alt jsabbutton"><?php echo $text; ?></a>
				<?php
			}
			if ($type == 'url'){
				$displaybutton=true;
				$text=get_post_meta($_product->id,'button_text',true);
				$productlink=get_post_meta($_product->id,'button_link',true);
				?>
				<a href="<?php echo $productlink; ?>" class="button"><?php echo $text; ?></a>
				<?php
			}
			}
	}
}
if (!function_exists('jigoshop_virtual_add_to_cart')) {
	function jigoshop_virtual_add_to_cart() {
	    global $_product;
       	// Type of Button
		$button=get_post_meta($_product->id,'button_type',true);
		$displaybutton=false;
		$type='none';
		$text='Sample Text';
		if ($button == 'productidtocart'){
		$displaybutton=true;
		$text=get_post_meta($_product->id,'button_text',true);
		$producttoadd=get_post_meta($_product->id,'product_number_button',true);
		$type='idcart';
		}
		if ($button == 'productidredirect'){
		$displaybutton=true;
		$text=get_post_meta($_product->id,'button_text',true);
		$productlink=get_post_meta($_product->id,'product_number_button',true);
		$type='idredirect';
		}
		if ($button == 'url'){
		$displaybutton=true;
		$text=get_post_meta($_product->id,'button_text',true);
		$productlink=get_post_meta($_product->id,'button_link',true);
		$type='url';
		}
		jigoshop_simple_add_to_cart();
			if ($displaybutton==true){ 
			if ($type == 'idcart'){
				$displaybutton=true;
				$text=get_post_meta($_product->id,'button_text',true);
				$producttoadd=get_post_meta($_product->id,'product_number_button',true);
				$url = add_query_arg('add-to-cart', $producttoadd);
				$link = jigoshop::nonce_url( 'add_to_cart', $url );
				?>
			<form action="<?php echo $link; ?>" class="jsabbutton" method="post">
			<?php do_action('jigoshop_before_add_to_cart_form_button'); ?>
		 	<input type="hidden" name="quantity" value="1" title="Qty" class="hiddenfield"/>
		 	<button type="submit" class="button-alt"><?php echo $text; ?></button>
			<?php 
			do_action('jigoshop_add_to_cart_form'); ?>
			</form>	
			<?php
			}
			if ($type == 'idredirect'){
				$displaybutton=true;
				$text=get_post_meta($_product->id,'button_text',true);
				$number=get_post_meta($_product->id,'product_number_button',true);
				$productlink=get_permalink( $number);
				?>
				<a href="<?php echo $productlink; ?>" class="button-alt jsabbutton"><?php echo $text; ?></a>
				<?php
			}
			if ($type == 'url'){
				$displaybutton=true;
				$text=get_post_meta($_product->id,'button_text',true);
				$productlink=get_post_meta($_product->id,'button_link',true);
				?>
				<a href="<?php echo $productlink; ?>" class="button"><?php echo $text; ?></a>
				<?php
			}
			}
	}
}
if (!function_exists('jigoshop_variable_add_to_cart')) {
	function jigoshop_variable_add_to_cart() {

		global $post, $_product;
        $jigoshop_options = Jigoshop_Base::get_options();
				// Type of Button
		$button=get_post_meta($_product->id,'button_type',true);
		$displaybutton=false;
		$type='none';
		$text='Sample Text';
		if ($button == 'productidtocart'){
		$displaybutton=true;
		$text=get_post_meta($_product->id,'button_text',true);
		$producttoadd=get_post_meta($_product->id,'product_number_button',true);
		$type='idcart';
		}
		if ($button == 'productidredirect'){
		$displaybutton=true;
		$text=get_post_meta($_product->id,'button_text',true);
		$productlink=get_post_meta($_product->id,'product_number_button',true);
		$type='idredirect';
		}
		if ($button == 'url'){
		$displaybutton=true;
		$text=get_post_meta($_product->id,'button_text',true);
		$productlink=get_post_meta($_product->id,'button_link',true);
		$type='url';
		}
		$attributes = $_product->get_available_attributes_variations();

        //get all variations available as an array for easy usage by javascript
        $variationsAvailable = array();
        $children = $_product->get_children();

        foreach($children as $child) {
            /* @var $variation jigoshop_product_variation */
            $variation = $_product->get_child( $child );
            if($variation instanceof jigoshop_product_variation) {
                $vattrs = $variation->get_variation_attributes();
                $availability = $variation->get_availability();

                //@todo needs to be moved to jigoshop_product_variation class
                if (has_post_thumbnail($variation->get_variation_id())) {
                    $attachment_id = get_post_thumbnail_id( $variation->get_variation_id() );
                    $large_thumbnail_size = apply_filters('single_product_large_thumbnail_size', 'shop_large');
                    $image = wp_get_attachment_image_src( $attachment_id, $large_thumbnail_size);
                    if ( ! empty( $image ) ) $image = $image[0];
                    $image_link = wp_get_attachment_image_src( $attachment_id, 'full');
                    if ( ! empty( $image_link ) ) $image_link = $image_link[0];
                } else {
                    $image = '';
                    $image_link = '';
                }

				$a_weight = $a_length = $a_width = $a_height = '';

                if ( $variation->get_weight() ) {
                	$a_weight = '
                    	<tr class="weight">
                    		<th>Weight</th>
                    		<td>'.$variation->get_weight().$jigoshop_options->get_option('jigoshop_weight_unit').'</td>
                    	</tr>';
            	}

            	if ( $variation->get_length() ) {
	            	$a_length = '
	                	<tr class="length">
	                		<th>Length</th>
	                		<td>'.$variation->get_length().$jigoshop_options->get_option('jigoshop_dimension_unit').'</td>
	                	</tr>';
                }

                if ( $variation->get_width() ) {
	                $a_width = '
	                	<tr class="width">
	                		<th>Width</th>
	                		<td>'.$variation->get_width().$jigoshop_options->get_option('jigoshop_dimension_unit').'</td>
	                	</tr>';
                }

                if ( $variation->get_height() ) {
	                $a_height = '
	                	<tr class="height">
	                		<th>Height</th>
	                		<td>'.$variation->get_height().$jigoshop_options->get_option('jigoshop_dimension_unit').'</td>
	                	</tr>
	                ';
            	}

                $variationsAvailable[] = array(
					'variation_id'     => $variation->get_variation_id(),
					'sku'              => '<div class="sku">'.__('SKU','jigoshop').': ' . $variation->get_sku() . '</div>',
					'attributes'       => $vattrs,
					'in_stock'         => $variation->is_in_stock(),
					'image_src'        => $image,
					'image_link'       => $image_link,
					'price_html'       => '<span class="price">'.$variation->get_price_html().'</span>',
					'availability_html'=> '<p class="stock ' . esc_attr( $availability['class'] ) . '">'. $availability['availability'].'</p>',
					'a_weight'         => $a_weight,
					'a_length'         => $a_length,
					'a_width'          => $a_width,
					'a_height'         => $a_height,
                );
            }
        }

		?>
        <script type="text/javascript">
            var product_variations = <?php echo json_encode($variationsAvailable) ?>;
        </script>
		<form action="<?php echo esc_url( $_product->add_to_cart_url() ); ?>" class="variations_form cart" method="post">
			<fieldset class="variations">
				<?php foreach ( $attributes as $name => $options ): ?>
					<?php $sanitized_name = sanitize_title( $name ); ?>
					<div>
						<span class="select_label"><?php echo jigoshop_product::attribute_label('pa_'.$name); ?></span>
						<select id="<?php echo esc_attr( $sanitized_name ); ?>" name="tax_<?php echo $sanitized_name; ?>">
							<option value=""><?php echo __('Choose an option ', 'jigoshop') ?>&hellip;</option>
							<?php foreach ( $options as $value ) : ?>
								<?php if ( taxonomy_exists( 'pa_'.$sanitized_name )) : ?>
									<?php $term = get_term_by( 'slug', $value, 'pa_'.$sanitized_name ); ?>
									<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo $term->name; ?></option>
								<?php else : ?>
									<option value="<?php echo esc_attr( sanitize_title( $value ) ); ?>"><?php echo $value; ?></option>
								<?php endif;?>
							<?php endforeach; ?>
						</select>
					</div>
                <?php endforeach;?>
			</fieldset>
			<div class="single_variation"></div>
			<?php do_action('jigoshop_before_add_to_cart_form_button'); ?>
			<div class="variations_button" style="display:none;">
                <input type="hidden" name="variation_id" value="" />
                <input type="hidden" name="product_id" value="<?php echo esc_attr( $post->ID ); ?>" />
                <div class="quantity"><input name="quantity" value="1" size="4" title="Qty" class="input-text qty text" maxlength="12" /></div>
				<input type="submit" class="button-alt" value="<?php esc_html_e('Add to cart', 'jigoshop'); ?>" />
			</div>
			<?php do_action('jigoshop_add_to_cart_form'); ?>
		</form>
		<?php
			if ($displaybutton==true){ 
			if ($type == 'idcart'){
				$displaybutton=true;
				$text=get_post_meta($_product->id,'button_text',true);
				$producttoadd=get_post_meta($_product->id,'product_number_button',true);
				$url = add_query_arg('add-to-cart', $producttoadd);
				$link = jigoshop::nonce_url( 'add_to_cart', $url );
				?>
			<form action="<?php echo $link; ?>" class="jsabbutton" method="post">
			<?php do_action('jigoshop_before_add_to_cart_form_button'); ?>
		 	<input type="hidden" name="quantity" value="1" title="Qty" class="hiddenfield"/>
		 	<button type="submit" class="button-alt"><?php echo $text; ?></button>
			<?php 
			do_action('jigoshop_add_to_cart_form'); ?>
			</form>	
			<?php
			}
			if ($type == 'idredirect'){
				$displaybutton=true;
				$text=get_post_meta($_product->id,'button_text',true);
				$number=get_post_meta($_product->id,'product_number_button',true);
				$productlink=get_permalink( $number);
				?>
				<a href="<?php echo $productlink; ?>" class="button-alt jsabbutton"><?php echo $text; ?></a>
				<?php
			}
			if ($type == 'url'){
				$displaybutton=true;
				$text=get_post_meta($_product->id,'button_text',true);
				$productlink=get_post_meta($_product->id,'button_link',true);
				?>
				<a href="<?php echo $productlink; ?>" class="button"><?php echo $text; ?></a>
				<?php
			}
			}
	}
}

if (!function_exists('jigoshop_external_add_to_cart')) {
	function jigoshop_external_add_to_cart() {
		global $_product;
				// Type of Button
		$button=get_post_meta($_product->id,'button_type',true);
		$displaybutton=false;
		$type='none';
		$text='Sample Text';
		if ($button == 'productidtocart'){
		$displaybutton=true;
		$text=get_post_meta($_product->id,'button_text',true);
		$producttoadd=get_post_meta($_product->id,'product_number_button',true);
		$type='idcart';
		}
		if ($button == 'productidredirect'){
		$displaybutton=true;
		$text=get_post_meta($_product->id,'button_text',true);
		$productlink=get_post_meta($_product->id,'product_number_button',true);
		$type='idredirect';
		}
		if ($button == 'url'){
		$displaybutton=true;
		$text=get_post_meta($_product->id,'button_text',true);
		$productlink=get_post_meta($_product->id,'button_link',true);
		$type='url';
		}
		$external_url = get_post_meta( $_product->ID, 'external_url', true );

		if ( ! $external_url )
			return false;
		?>

		<p>
			<a href="<?php echo esc_url( $external_url ); ?>" rel="nofollow" class="button"><?php _e('Buy product', 'jigoshop'); ?></a>
			<?php
			if ($displaybutton==true){ 
			if ($type == 'idcart'){
				$displaybutton=true;
				$text=get_post_meta($_product->id,'button_text',true);
				$producttoadd=get_post_meta($_product->id,'product_number_button',true);
				$url = add_query_arg('add-to-cart', $producttoadd);
				$link = jigoshop::nonce_url( 'add_to_cart', $url );
				?>
			<form action="<?php echo $link; ?>" class="jsabbutton" method="post">
			<?php do_action('jigoshop_before_add_to_cart_form_button'); ?>
		 	<input type="hidden" name="quantity" value="1" title="Qty" class="hiddenfield"/>
		 	<button type="submit" class="button-alt"><?php echo $text; ?></button>
			<?php 
			do_action('jigoshop_add_to_cart_form'); ?>
			</form>	
			<?php
			}
			if ($type == 'idredirect'){
				$displaybutton=true;
				$text=get_post_meta($_product->id,'button_text',true);
				$number=get_post_meta($_product->id,'product_number_button',true);
				$productlink=get_permalink( $number);
				?>
				<a href="<?php echo $productlink; ?>" class="button-alt jsabbutton"><?php echo $text; ?></a>
				<?php
			}
			if ($type == 'url'){
				$displaybutton=true;
				$text=get_post_meta($_product->id,'button_text',true);
				$productlink=get_post_meta($_product->id,'button_link',true);
				?>
				<a href="<?php echo $productlink; ?>" class="button"><?php echo $text; ?></a>
				<?php
			}
			}?>
		</p>

		<?php
	}
}
add_action( 'init', 'jigoshop_add_to_cart_action' );
if ( ! function_exists( 'jigoshop_add_to_cart_action' )) { //make function pluggable
	function jigoshop_add_to_cart_action( $url = false ) {

		if ( empty($_REQUEST['add-to-cart']) || !jigoshop::verify_nonce('add_to_cart') )
			return false;

		$jigoshop_options = Jigoshop_Base::get_options();

		$product_added = false;

		switch ( $_REQUEST['add-to-cart'] ) {

			case 'variation':

				if ( empty($_REQUEST['variation_id']) || !is_numeric($_REQUEST['variation_id']) ) {
					jigoshop::add_error( __('Please choose product options&hellip;', 'jigoshop') );
					wp_safe_redirect( apply_filters('jigoshop_product_id_add_to_cart_filter', get_permalink($_REQUEST['product_id'])) );
					exit;
				}

				$product_id   = apply_filters('jigoshop_product_id_add_to_cart_filter',   (int) $_REQUEST['product_id']);
				$variation_id = apply_filters('jigoshop_variation_id_add_to_cart_filter', (int) $_REQUEST['variation_id']);
				$quantity     = (isset($_REQUEST['quantity'])) ? (int) $_REQUEST['quantity'] : 1;
				$attributes   = (array) maybe_unserialize(get_post_meta($product_id, 'product_attributes', true));
				$variations   = array();

				$all_variations_set = true;

				if ( get_post_meta( $product_id , 'customizable', true ) == 'yes' ) {
					// session personalization initially set to parent product until variation selected
					$custom_products = (array) jigoshop_session::instance()->customized_products;
					// transfer it to the variation
					$custom_products[$variation_id] = $custom_products[$product_id];
					unset( $custom_products[$product_id] );
					jigoshop_session::instance()->customized_products = $custom_products;
				}

				foreach ($attributes as $attribute) {

					if ( !$attribute['variation'] )
						continue;

					$attr_name = 'tax_' . sanitize_title($attribute['name']);
					if ( !empty($_REQUEST[$attr_name]) ) {
						$variations[$attr_name] = esc_attr($_REQUEST[$attr_name]);
					} else {
						$all_variations_set = false;
					}
				}

				// Add to cart validation
				$is_valid = apply_filters('jigoshop_add_to_cart_validation', true, $product_id, $quantity);

				if ( $all_variations_set && $is_valid ) {
					jigoshop_cart::add_to_cart($product_id, $quantity, $variation_id, $variations);
					$product_added = true;
				}
			break;

			case 'group':

				if ( empty($_REQUEST['quantity']) || !is_array($_REQUEST['quantity']) )
					break; // do nothing

				foreach ( $_REQUEST['quantity'] as $product_id => $quantity ) {

					// Skip if no quantity
					if ( ! $quantity )
						continue;

					$quantity = (int) $quantity;

					// Add to cart validation
					$is_valid = apply_filters('jigoshop_add_to_cart_validation', true, $product_id, $quantity);

					// Add to the cart if passsed validation
					if ( $is_valid ) {
						jigoshop_cart::add_to_cart($product_id, $quantity);
						$product_added = true;
					}
				}

			break;

			default:

				if ( !is_numeric($_REQUEST['add-to-cart']) )
					// Handle silently for now
					break;

				// Get product ID & quantity
				$product_id = apply_filters('jigoshop_product_id_add_to_cart_filter', (int) $_GET['add-to-cart']);
				$quantity   = (isset($_REQUEST['quantity'])) ? (int) $_REQUEST['quantity'] : 1;

				// Add to cart validation
				$is_valid   = apply_filters('jigoshop_add_to_cart_validation', true, $product_id, $quantity);

				// Add to the cart if passsed validation
				if ( $is_valid ) {
					jigoshop_cart::add_to_cart($product_id, $quantity);
					$product_added = true;
				}
	
				$redirect=get_post_meta($product_id,'redirection_type',true);
				if ($redirect == 'productid'){
					$id=get_post_meta($product_id,'product_number_redirect',true);
					$link=get_permalink( $id);
					wp_redirect($link); exit;
				}
				if ($redirect == 'url'){
					$url=get_post_meta($product_id,'redirect_link',true);
					wp_redirect($url); exit;
				}
		break;
		}

		if ( ! $product_added ) {
			jigoshop::add_error( __('Product could not be added to the cart', 'jigoshop') );
			return false;
		}

		switch ( $jigoshop_options->get_option('jigoshop_redirect_add_to_cart', 'same_page') ) {
			case 'same_page':
				$message = __('Product successfully added to your cart.', 'jigoshop');
				$button = __('View Cart &rarr;', 'jigoshop');
				$message = '<a href="%s" class="button">' . $button . '</a> ' . $message;
				jigoshop::add_message(sprintf( $message, jigoshop_cart::get_cart_url()));
				break;

			case 'to_checkout':
					// Do nothing
				break;

			default:
				jigoshop::add_message(__('Product successfully added to your cart.', 'jigoshop'));
				break;
		}
		
		if ( apply_filters('add_to_cart_redirect', $url) ) {
			wp_safe_redirect($url); exit;
		}
		else if ( $jigoshop_options->get_option('jigoshop_redirect_add_to_cart', 'same_page') == 'to_checkout' && !jigoshop::has_errors() ) {
			wp_safe_redirect(jigoshop_cart::get_checkout_url()); exit;
		}
		else if ($jigoshop_options->get_option('jigoshop_redirect_add_to_cart', 'to_cart') == 'to_cart' && !jigoshop::has_errors()) {
			wp_safe_redirect(jigoshop_cart::get_cart_url()); exit;
		}
		else if ( wp_get_referer() ) {
			wp_safe_redirect( remove_query_arg( array( 'add-to-cart', 'quantity', 'product_id' ), wp_get_referer() ) ); exit;
		}
		else {
			wp_safe_redirect(home_url()); exit;
		}
	}
}