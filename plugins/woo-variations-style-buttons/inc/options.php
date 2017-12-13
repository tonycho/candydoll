<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class ED_WC_VAR_Options_Maker {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'WC_VAR_add_admin_menu' )  );
		add_action( 'admin_init', array( $this, 'WC_VAR_settings_init' )  );
		add_action('admin_enqueue_scripts', array( $this, 'ed_wc_variations_enqueue' ));
	}
	public function WC_VAR_add_admin_menu(  ) {

	//add_submenu_page( 'tools.php', 'hello world', 'hello world', 'manage_options', 'hello_world', array( $this, 'WC_VAR_options_page' ) );
add_menu_page( 'Woo Variations Style', 'Woo Variations Style', 'manage_options', 'ed_wc_var', array( $this, 'WC_VAR_options_page' ) );
	}

	function WC_VAR_settings_init(  ) {


	if ( $attribute_taxonomies = wc_get_attribute_taxonomies() ) :
	$ed_options = get_option( WC_VAR_SETTINGS );

		foreach ( $attribute_taxonomies as $tax ) :

				$settings_name = WC_PREFIX.'_'.wc_attribute_taxonomy_name( $tax->attribute_name );
				register_setting( $settings_name, WC_VAR_SETTINGS );

				$link_default_class = 'ed_show_link_button';
				$color_default_class = 'ed_show_color_button';
				$image_default_class = 'ed_show_image_button';
				if($ed_options == NULL){
					$link_default_class = 'ed_show_link_button ed_get_value_and_this';
				}
				if(isset($ed_options[$settings_name]['button_type']) && $ed_options[$settings_name]['button_type']==1){
					$link_default_class = 'ed_show_link_button ed_get_value_and_this';
				}else if(isset($ed_options[$settings_name]['button_type']) && $ed_options[$settings_name]['button_type']==2){
					$color_default_class = 'ed_show_color_button ed_get_value_and_this';
				}else if(isset($ed_options[$settings_name]['button_type']) && $ed_options[$settings_name]['button_type']==3){
					$image_default_class = 'ed_show_image_button ed_get_value_and_this';
				}else if(isset($ed_options[$settings_name]['button_type']) && $ed_options[$settings_name]['button_type']==4){
					$link_default_class =  '';
				}else if(isset($ed_options[$settings_name]['button_type']) && $ed_options[$settings_name]['button_type']==5){
					$link_default_class =  'ed_show_link_button';
				}else {
					$link_default_class = 'ed_show_link_button ed_get_value_and_this';
				}



				add_settings_section(
					WC_PREFIX.'_section',
					'',
					array( $this, 'WC_VAR_settings_section_callback' ),
					$settings_name /* action recieve */
				);

				add_settings_field(
					WC_PREFIX.'_Button_Type',
					__( 'Button Type', 'WC_VAR_LANG' ),
					array( $this, 'ed_print_select_field' ),
					$settings_name,
					WC_PREFIX.'_section',
					$args     = array (
						'name' => 'button_type',
						'options' =>array(
							'1' => 'Link Button',
							'2' => 'Color Button',
							'3' => 'Image Button',
							'4' => 'Radio Button',
						),
						'settings' => $settings_name,
						'group' => "button_type",


					)
				);
				add_settings_field(
					WC_PREFIX.'_Button_Style',
					__( 'Button Style', 'WC_VAR_LANG' ),
					array( $this, 'ed_print_select_field' ),
					$settings_name,
					WC_PREFIX.'_section',
					$args     = array (
						'name' => 'button_style',
						'options' =>array(
							'1' => 'Square',
							'2' => 'Round',
						),
						'settings' => $settings_name,

						'class' => 'common_hide ed_show_link_button '.$link_default_class.' '.$color_default_class
					)
				);

				add_settings_field(
					WC_PREFIX.'_Wdith',
					__( 'Width', 'WC_VAR_LANG' ),
					array( $this, 'print_input_number_field' ),
					$settings_name,
					WC_PREFIX.'_section',
					$args     = array (
						'name' => 'wdith',
						'settings' => $settings_name,
						'class' => 'common_hide ed_show_link_button '.$link_default_class.' '.$color_default_class.' '.$image_default_class
					)
				);
				add_settings_field(
					WC_PREFIX.'_Height',
					__( 'Height', 'WC_VAR_LANG' ),
					array( $this, 'print_input_number_field' ),
					$settings_name,
					WC_PREFIX.'_section',
					$args     = array (
						'name' => 'height',
						'settings' => $settings_name,
						'class' => 'common_hide ed_show_link_button '.$link_default_class.' '.$color_default_class.' '.$image_default_class
					)
				);
				add_settings_field(
					WC_PREFIX.'_Size',
					__( 'Text Size', 'WC_VAR_LANG' ),
					array( $this, 'print_input_field' ),
					$settings_name,
					WC_PREFIX.'_section',
					$args     = array (
						'name' => 'text_size',
						'settings' => $settings_name,
						'class' => 'common_hide ed_show_link_button '.$link_default_class

					)
				);
				add_settings_field(
					WC_PREFIX.'_Text_Color',
					__( 'Text Color', 'WC_VAR_LANG' ),
					array( $this, 'print_input_field' ),
					$settings_name,
					WC_PREFIX.'_section',
					$args     = array (
						'name' => 'text_color',
						'group' => "ed-color-field",
						'settings' => $settings_name,
						'class' => 'common_hide ed_show_link_button '.$link_default_class
					)
				);
				add_settings_field(
					WC_PREFIX.'_Text_Color_Hover',
					__( 'Text Hover Color', 'WC_VAR_LANG' ),
					array( $this, 'print_input_field' ),
					$settings_name,
					WC_PREFIX.'_section',
					$args     = array (
						'name' => 'text_color_hover',
						'group' => "ed-color-field",
						'settings' => $settings_name,
						'class' => 'common_hide ed_show_link_button '.$link_default_class
					)
				);
				add_settings_field(
					WC_PREFIX.'_Bg',
					__( 'Background Color', 'WC_VAR_LANG' ),
					array( $this, 'print_input_field' ),
					$settings_name,
					WC_PREFIX.'_section',
					$args     = array (
						'name' => 'bg',
						'group' => "ed-color-field",
						'settings' => $settings_name,
						'class' => 'common_hide ed_show_link_button '.$link_default_class
					)
				);
				add_settings_field(
					WC_PREFIX.'_Bg_Hover',
					__( 'Background Color Hover', 'WC_VAR_LANG' ),
					array( $this, 'print_input_field' ),
					$settings_name,
					WC_PREFIX.'_section',
					$args     = array (
						'name' => 'bg_hover',
						'group' => "ed-color-field",
						'settings' => $settings_name,
						'class' => 'common_hide ed_show_link_button '.$link_default_class
					)
				);
				add_settings_field(
					WC_PREFIX.'_Border',
					__( 'Border Color', 'WC_VAR_LANG' ),
					array( $this, 'print_input_field' ),
					$settings_name,
					WC_PREFIX.'_section',
					$args     = array (
						'name' => 'border',
						'group' => "ed-color-field",
						'settings' => $settings_name,
						'class' => 'common_hide ed_show_link_button '.$link_default_class
					)
				);

				add_settings_field(
					WC_PREFIX.'_Border_Hover',
					__( 'Border Color Hover', 'WC_VAR_LANG' ),
					array( $this, 'print_input_field' ),
					$settings_name,
					WC_PREFIX.'_section',
					$args     = array (
						'name' => 'border_hover',
						'group' => "ed-color-field",
						'settings' => $settings_name,
						'class' => 'common_hide ed_show_link_button '.$link_default_class
					)
				);

				add_settings_field(
					WC_PREFIX.'_image_form',
					__( 'Image Load Form', 'WC_VAR_LANG' ),
					array( $this, 'ed_print_select_field' ),
					$settings_name,
					WC_PREFIX.'_section',
					$args     = array (
						'name' => 'image_form',
						'options' =>array(
							'1' => 'Woo Variations Style',
							'2' => 'Woocommerce Product Variations ',
						),
						'settings' => $settings_name,

						'class' => 'common_hide '.$image_default_class
					)
				);
				$args = array( 'hide_empty' => false);
				$terms = get_terms(wc_attribute_taxonomy_name( $tax->attribute_name ),$args);
				if(isset($terms) && count($terms) > 0){
					$i = 0;
					foreach($terms as $key => $val){ $i++;
						if($i <= 3) {
							add_settings_field(
								WC_PREFIX.$val->term_id,
								'Choose Color For '.$val->name,
								array( $this, 'print_input_active_field' ),
								$settings_name,
								WC_PREFIX.'_section',
								$args     = array (
									'name' => $val->term_id,
									'group' => "ed-color-field",
									'settings' => 'color_button_'.wc_attribute_taxonomy_name( $tax->attribute_name ),
									'class' => 'common_hide '.$color_default_class
								)
							);

							add_settings_field(
								WC_PREFIX.'_image_button_'.$val->term_id,
								'Choose Image For ' . $val->name ,
								array( $this, 'print_input_images_active_field' ),
								$settings_name,
								WC_PREFIX.'_section',
								$args     = array (
									'name' => $val->term_id,
									'terms_name' => $val->name,
									'settings' => 'ed_images_button_'.wc_attribute_taxonomy_name( $tax->attribute_name ),
									'class' => 'common_hide '.$image_default_class
								)
							);
						}else{
							add_settings_field(
								WC_PREFIX.$val->term_id,
								'Choose Color For '.$val->name,
								array( $this, 'look_color_print_input_field' ),
								$settings_name,
								WC_PREFIX.'_section',
								$args     = array (
									'name' => $val->term_id,
									'group' => "ed-color-field",
									'settings' => 'color_button_'.wc_attribute_taxonomy_name( $tax->attribute_name ),
									'class' => 'common_hide '.$color_default_class
								)
							);

							add_settings_field(
								WC_PREFIX.'_image_button_'.$val->term_id,
								'Choose Image For ' . $val->name ,
								array( $this, 'print_input_images_field' ),
								$settings_name,
								WC_PREFIX.'_section',
								$args     = array (
									'name' => $val->term_id,
									'terms_name' => $val->name,
									'settings' => 'ed_images_button_'.wc_attribute_taxonomy_name( $tax->attribute_name ),
									'class' => 'common_hide '.$image_default_class
								)
							);
						}

					}
				}
		endforeach; endif;

	}


	function print_input_field( array $args )
	{
		$options = get_option( WC_VAR_SETTINGS );
		$group = (isset($args['group']) && $args['group'] != "")? $args['group']:'';
	?>

		<input type='text' class="<?php echo $group;?>" name='<?php echo WC_VAR_SETTINGS;?>[<?php echo $args['settings'];?>][<?php echo $args['name'];?>]' value='<?php echo isset($options[$args['settings']][$args['name']]) ? $options[$args['settings']][$args['name']] : ''; ?>'>
		<p style="font-size:12px; font-style:italic; color:#aaa;"><a href="http://edatastyle.com/product/woo-variations-style-buttons/" target="_blank">PRO version</a></p>
        <input type="hidden" name='<?php echo WC_VAR_SETTINGS;?>[<?php echo $args['settings'];?>][<?php echo $args['name'];?>]' value='<?php echo isset($options[$args['settings']][$args['name']]) ? $options[$args['settings']][$args['name']] : ''; ?>'>
	<?php
	}
	function print_input_active_field( array $args )
	{
		$options = get_option( WC_VAR_SETTINGS );
		$group = (isset($args['group']) && $args['group'] != "")? $args['group']:'';
	?>

		<input type='text' class="<?php echo $group;?>" name='<?php echo WC_VAR_SETTINGS;?>[<?php echo $args['settings'];?>][<?php echo $args['name'];?>]' value='<?php echo isset($options[$args['settings']][$args['name']]) ? $options[$args['settings']][$args['name']] : ''; ?>'>
		<?php
	}

	function look_color_print_input_field( array $args )
	{
		$options = get_option( WC_VAR_SETTINGS );
		$group = (isset($args['group']) && $args['group'] != "")? $args['group']:'';
	?>

		<input type='text' class="<?php echo $group;?>" name='<?php echo WC_VAR_SETTINGS;?>[<?php echo $args['settings'];?>][<?php echo $args['name'];?>]' value='<?php echo isset($options[$args['settings']][$args['name']]) ? $options[$args['settings']][$args['name']] : ''; ?>'>
		<p style="font-size:12px; font-style:italic; color:#aaa;"><a href="http://edatastyle.com/product/woo-variations-style-buttons/" target="_blank">PRO version</a></p>
        <input type="hidden" name='<?php echo WC_VAR_SETTINGS;?>[<?php echo $args['settings'];?>][<?php echo $args['name'];?>]' value='<?php echo $options[$args['settings']][$args['name']]; ?>'>
	 <p style="font-size:12px; font-style:italic; color:#aaa;">upgrade Pro To unlock all Color Picker</p>
	<?php
	}


	function print_input_number_field( array $args )
	{
		$options = get_option( WC_VAR_SETTINGS );
		$group = (isset($args['group']) && $args['group'] != "")? $args['group']:'';
	?>
		<input type="number" class="<?php echo $group;?>" name='<?php echo WC_VAR_SETTINGS;?>[<?php echo $args['settings'];?>][<?php echo $args['name'];?>]' value='<?php echo isset($options[$args['settings']][$args['name']])? $options[$args['settings']][$args['name']] :''; ?>'>
		<span>PX</span>
        <p style="font-size:12px; font-style:italic; color:#aaa;">PRO version</p>
	<?php
	}


	function print_input_images_field( array $args )
	{
		$options = get_option( WC_VAR_SETTINGS );
		$group = (isset($args['group']) && $args['group'] != "")? $args['group']:'';

	?>
		<input size="30" type='text' class=" <?php echo $group;?>" name='<?php echo WC_VAR_SETTINGS;?>[<?php echo $args['settings'];?>][<?php echo $args['name'];?>]' value='<?php echo isset($options[$args['settings']][$args['name']]) ? $options[$args['settings']][$args['name']] : ''; ?>'>
		<a href="#" class="button ed__load__image" data-uploader-title="Add image(s) to <?php echo $args['terms_name'].' '.__( 'Attributes', 'woocommerce' );?>" data-uploader-button-text="Add image(s) <?php echo $args['terms_name'];?>">Upload</a>
	<input type="hidden" name='<?php echo WC_VAR_SETTINGS;?>[<?php echo $args['settings'];?>][<?php echo $args['name'];?>]' value="">
	 <p style="font-size:12px; font-style:italic; color:#aaa;">upgrade Pro To unlock all image Uploader</p>
	<?php
	}
	function print_input_images_active_field( array $args )
	{
		$options = get_option( WC_VAR_SETTINGS );
		$group = (isset($args['group']) && $args['group'] != "")? $args['group']:'';

	?>
		<input size="30" type='text' class=" <?php echo $group;?>" name='<?php echo WC_VAR_SETTINGS;?>[<?php echo $args['settings'];?>][<?php echo $args['name'];?>]' value='<?php echo isset($options[$args['settings']][$args['name']]) ? $options[$args['settings']][$args['name']] : ''; ?>'>
		<a href="#" class="button ed__load__image" data-uploader-title="Add image(s) to <?php echo $args['terms_name'].' '.__( 'Attributes', 'woocommerce' );?>" data-uploader-button-text="Add image(s) <?php echo $args['terms_name'];?>">Upload</a>

	<?php
	}


	function ed_print_select_field( array $args )
	{
		$options = get_option( WC_VAR_SETTINGS );
		$group = (isset($args['group']) && $args['group'] != "")? $args['group']:'';

		if($args['name']=='image_form'):  $disabled = 'disabled="disabled"'; else: $disabled=''; endif;
	?>
		<select  <?php echo $disabled;?> class="<?php echo $group;?>" name='<?php echo WC_VAR_SETTINGS;?>[<?php echo $args['settings'];?>][<?php echo $args['name'];?>]'>
			<?php if(isset($args['options']) && count($args['options']) >0 ):
			$checked = isset($options[$args['settings']][$args['name']]) ? $options[$args['settings']][$args['name']] : '';
				foreach ( $args['options'] as $key => $val):
			 ?>
			<option value='<?php echo $key;?>' <?php selected( $checked, $key ); ?>><?php echo $val;?></option>
			<?php endforeach; endif;?>

		</select>
        <?php if($args['name']=='image_form'):?>
        <p style="font-size:12px; font-style:italic; color:#aaa;"><a href="http://edatastyle.com/product/woo-variations-style-buttons/" target="_blank">PRO version</a></p>
        <?php endif;?>
	<?php
	}



	function WC_VAR_settings_section_callback(  ) {

		?>
		<?php

	}


	function WC_VAR_options_page(  ) {

		?>

		<div class="wrap">
		<h2><?php _e( 'Woocommerce  Variations Style Buttons Settings', 'WC_VAR_LANG' ); ?></h2>
		<p><?php _e( ' Enabling will make your Variations to display as buttons. you can set each variation(Attibute) to show as a color, text, image button, radio or select type and button.', 'WC_VAR_LANG' ); ?></p>

		<div id="wpcom-stats-meta-box-container" class="metabox-holder">

			<form method="post" action="options.php">

			<?php if ( $attribute_taxonomies = wc_get_attribute_taxonomies() ) :
							$i = 0; $j = 0;

								foreach ( $attribute_taxonomies as $tax ) : $i++; $j++;
								if($i == 1){echo '<div class="ed-create-column">';}

				?>

			<div class="postbox-container" style="width:95%;">

				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<div class="postbox" id="ed-global-settings">
						<h3 class="hndle"><span><?php echo $tax->attribute_label.' Variation( '.__( 'Attributes', 'woocommerce' ) .') '. __( 'Settings', 'woocommerce' ); ?></span></h3>
						<div class="inside ed__settings__loops">

								<?php
								$settings_name = WC_PREFIX.'_'.wc_attribute_taxonomy_name( $tax->attribute_name );
								settings_fields( $settings_name );
								do_settings_sections( $settings_name );

								?>




						</div>
						<div id="major-publishing-actions">
							<div id="publishing-action">
								<?php echo  submit_button();?>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>

			</div>
			 <?php
				if($i == round(count($attribute_taxonomies)/2)){
						echo '</div>';
						$i = 0;
				}else{
					if(count($attribute_taxonomies) == $j){
						echo '</div>';
					}
				}
			 endforeach;?>

             <?php endif;?>
			 </form>

		</div>
	</div>

		<?php

	}

	function ed_wc_variations_enqueue($hook) {
	// admin utilities
	wp_enqueue_media();
	 // wp core styles
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_style( 'wp-jquery-ui-dialog' );;

	 // wp core scripts
    wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_script( 'jquery-ui-dialog' );
    wp_enqueue_script( 'jquery-ui-sortable' );
    wp_enqueue_script( 'jquery-ui-accordion' );

	wp_enqueue_style( 'ed-variations-style-buttons-admin',WC_VAR_URL. 'assets/admin.css' );
	wp_enqueue_script('ed-wc-variations-admin-js', WC_VAR_URL . '/assets/admin.js', array('jquery', 'jquery-ui-sortable'));
	}

}


new ED_WC_VAR_Options_Maker();
