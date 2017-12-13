<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class WC_Variations_Style_Buttons_Meta_Tabs {
	public $meta_name = '__ed_woo_meta_settings';
	public function __construct() {
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'eds_add_my_custom_product_data_tab' ), 99 , 1 );
			add_action( 'woocommerce_product_data_panels', array( $this, 'eds_add_my_custom_product_data_fields' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'woocommerce_process_product_meta_fields_save' ));
		}
	public function eds_add_my_custom_product_data_tab( $product_data_tabs ) {
		$product_data_tabs['woo-variations-style'] = array(
			'label' => __( 'Woo Variations Style', 'WC_VAR_LANG' ),
			'target' => 'woo_variations_style',
			'class' => array( 'show_if_variable' )
		);
   		 return $product_data_tabs;
	}	
	public function eds_add_my_custom_product_data_fields() {
    global $woocommerce, $post;
	$settings_name = 'woo_var_style_button_meta_settings';
	
    ?>
    <!-- id below must match target registered in above add_my_custom_product_data_tab function -->
    <div id="woo_variations_style" class="panel woocommerce_options_panel hidden">
        
        <div id="woo_variations_style_tab_wrp" class="options_group">
          <table class="wcsap widefat">
                <thead>
                <tr><th class="attribute_swatch_label">
                    Product Attribute Name</th>
                    <th  align="right">
                    This settings only works for pro version <a href="https://edatastyle.com/product/woo-variations-style-buttons/" target="_blank">upgrade to pro &rarr;</a>
                    </th>
              </thead>
            </table>
       	<ul class="woo_variations_style_tab_container">  
                
                
        		<?php //$this->meta_name
				$meta_value = maybe_unserialize( get_post_meta( $post->ID ,$this->meta_name, true ));
				
				$meta = array();
				if ( $attribute_taxonomies = wc_get_attribute_taxonomies() ) :
					foreach ( $attribute_taxonomies as $tax ) :
					
						$settings_name = 'woo_var_style_button_meta_settings'.wc_attribute_taxonomy_name( $tax->attribute_name );
						if(isset($meta_value) && $meta_value != NULL){ $meta = $meta_value[wc_attribute_taxonomy_name($tax->attribute_name)];}
						
						$this->reander_woo_meta_section($settings_name,$tax->attribute_name,$tax->attribute_label,$meta);
					endforeach;
				endif;
				?>
        </ul> 
                </div> 
            </div>
            <?php
        }
	
	public function woocommerce_process_product_meta_fields_save( $post_id ){
		
		$woo_meta_data_update = isset( $_POST['__ed_woo_meta_settings'] ) ? maybe_serialize($_POST['__ed_woo_meta_settings']) : '';
		if($woo_meta_data_update != "") 
		update_post_meta( $post_id, '__ed_woo_meta_settings', $woo_meta_data_update );
	}	
	public function reander_woo_meta_section($settings_name,$attribute_name,$label,array $meta_value){
		$show_color =(isset($meta_value['woo_meta_button_type']) && $meta_value['woo_meta_button_type']==1)? 'show_default_meta': ''; 
		$show_img =( isset($meta_value['woo_meta_button_type'])  && $meta_value['woo_meta_button_type']==2)? 'show_default_meta': '';
		$settings_name = wc_attribute_taxonomy_name( $attribute_name );
		
		?>
        <li>
              <div class="eds_attribute_heading"> 
                <strong><a class="row-title" href="javascript:void(0)"><?php echo $label;?></a></strong>
              </div> 
        <div class="ed_woo_otions_wrp">         
        <table class="wcsap_input widefat wcsap_field_form_table">
            <tbody>
                <tr>
                    <td class="col_label">
                         <label><?php echo __( 'Type', 'WC_VAR_LANG' );?></label>
                    </td>
                    <td>
                    <?php
					$args     = array (
						'name' => 'woo_meta_button_type',
						'options' =>array(
							'default' => 'Plugins Global Settings',
							'1' => 'Color Button',
							'2' => 'Image Button',
						
						),
						'settings' => $settings_name,
						'class' => 'ed_woo_load_attribute_configuration',
						'value' =>  (isset($meta_value['woo_meta_button_type'])) ? $meta_value['woo_meta_button_type'] : ''
					);
					$this->ed_meta_print_select_field($args);
					?>
                    </td>
                </tr>
                
                <tr>
                    <td class="col_label">
                        <label><?php echo __( 'Button Style', 'WC_VAR_LANG' );?></label>
                    </td>
                    <td>
                    <?php
					$args     = array (
						'name' => 'button_style',
						'options' =>array(
							'1' => 'Square',
							'2' => 'Round',
						),
						'settings' => $settings_name,
						
						'value' =>  (isset($meta_value['button_style'])) ? $meta_value['button_style'] : ''
					);
					$this->ed_meta_print_select_field($args);
					?>
                    </td>
                </tr>
                 <tr class="ed_woo_var_color_attribute_configuration <?php echo $show_color;?>">
                    <td class="col_label">
                        <label><?php echo __( 'Attribute Configuration', 'WC_VAR_LANG' );?></label>
                    </td>
                    <td>
                         <div class="options_group">
                        <table class="wcsap widefat">
                            <tbody>
								<?php 
								$args = array( 'hide_empty' => false);
                                $terms = get_terms(wc_attribute_taxonomy_name( $attribute_name ),$args);
                                if(isset($terms) && count($terms) > 0){
                                    foreach($terms as $key => $val){
                                        ?>
                                        <tr><th class="attribute_swatch_label"><?php echo $val->name;?></th>
                                        <th class="attribute_swatch_type">
										<?php
                                        $args     = array (
                                        'name' => 'color__'.$val->term_id,
                                        'group' => "ed-color-field",
                                        'settings' => $settings_name,
                                        'class' => 'common_hide ',
										'value' =>  isset($meta_value['color__'.$val->term_id]) ? $meta_value['color__'.$val->term_id] : ''
                                        );
										$this->print_input_field($args);
                                        ?>
                                        
                                        </th>
                                        </tr>
                                        <?php
                                    }
                                } ?>
                           
                            </tbody>
                        </table>
                        
                        </div>
                    </td>
                </tr>
                
                 <tr class="ed_woo_var_images_attribute_configuration <?php echo $show_img;?>">
                    <td class="col_label">
                        <label><?php echo __( 'Attribute Configuration', 'WC_VAR_LANG' );?></label>
                    </td>
                    <td>
                         <div class="options_group">
                        <table class="wcsap widefat">
                            <tbody>
								<?php 
								$args = array( 'hide_empty' => false);
                                $terms = get_terms(wc_attribute_taxonomy_name( $attribute_name ),$args);
                                if(isset($terms) && count($terms) > 0){
                                    foreach($terms as $key => $val){
                                        ?>
                                        <tr><th class="attribute_swatch_label"><?php echo $val->name;?></th>
                                        <td class="attribute_woo_var_style_img_row">
										<?php
                                       
										$args     = array (
											'name' => 'img__'.$val->term_id,
											'terms_name' => $val->name,
											 'settings' => $settings_name,
											'class' => 'common_hide ',
											'value' =>  isset($meta_value['img__'.$val->term_id]) ? $meta_value['img__'.$val->term_id]: ''
										);
										$this->print_input_images_field($args);
                                        ?>
                                        
                                        </td>
                                        </tr>
                                        <?php
                                    }
                                } ?>
                           
                            </tbody>
                        </table>
                        
                        </div>
                    </td>
                </tr>
                 
            
            </tbody>
        </table>
        </div>
                    </li>
        <?php
	}
		
	public function ed_meta_print_select_field( array $args )
	{
		$class = (isset($args['class']) && $args['class'] != "")? $args['class']:'';
	?>
		<select class="<?php echo $class;?>" name='<?php echo $this->meta_name;?>[<?php echo $args['settings'];?>][<?php echo $args['name'];?>]'>
			<?php if(isset($args['options']) && count($args['options']) >0 ):
				foreach ( $args['options'] as $key => $val):
			 ?>
			<option value='<?php echo $key;?>' <?php selected( $args['value'], $key ); ?>><?php echo $val;?></option>
			<?php endforeach; endif;?>
			
		</select>
	<?php
	}
	function print_input_field( array $args )
	{
		$group = (isset($args['group']) && $args['group'] != "")? $args['group']:'';
	?>
		<input  type='text' class="<?php echo $group;?>" name='<?php echo $this->meta_name;?>[<?php echo $args['settings'];?>][<?php echo $args['name'];?>]' value='<?php echo $args['value']; ?>'>
		
	<?php
	}
	
	function print_input_images_field( array $args )
	{
		$group = (isset($args['group']) && $args['group'] != "")? $args['group']:'';
		
	?>
    	
    	<div class="ed_woo_img_wrp">
        <?php if($args['value']!=""):
		 $image = wp_get_attachment_image_src( $args['value'], 'thumbnail' ); 
		?><img src="<?php echo $image[0];?>"/><?php endif;?>
        </div>
        
		<input size="30" type="hidden"  name='<?php echo $this->meta_name;?>[<?php echo $args['settings'];?>][<?php echo $args['name'];?>]' value='<?php echo $args['value']; ?>'>
		<a ref="javascript:void(0)" class="remove_ed_woo_meta_img button">Remove image</a>
        <a href="javascript:void(0)" class="button ed_woo_meta_uploader" data-uploader-title="Add image(s) to <?php echo $args['terms_name'].' '.__( 'Attributes', 'woocommerce' );?>" data-uploader-button-text="Add image(s) <?php echo $args['terms_name'];?>">Upload/Add image</a>
		
	<?php
	}
}
new WC_Variations_Style_Buttons_Meta_Tabs();

