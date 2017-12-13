<?php
	$user_lang = get_post_meta( $order->id, '_user_language', true );
?>

<div style='height:100%; width:100%;'>
	<header >
		<a class="print" href="#" onclick="window.print()" ><?php _e('Print', 'wf-woocommerce-packing-list'); ?></a>
		<div style="float:left; width:35%; text-align:left;">
			<strong>
				<div style="font-size:<?php echo $title_size;?>px;  margin-top:15px;">
					<?php
						_e('Order: '.$order->id, 'wf-woocommerce-packing-list');
					?>
				</div>
			</strong>
			<div style="margin-top:10px; font-size:<?php echo $title_size;?>px;"><?php
			_e(('Order Date: '.date("d-m-Y", strtotime($order->order_date))) , 'wf-woocommerce-packing-list'); ?>
			</div>
		</div>
		<div style="float:left; width:45%; text-align:left; margin: 10px 20px 0 0;font-size:<?php echo $heading_size;?>px;">
			<?php _e($this->wf_packinglist_get_companyname() , 'wf-woocommerce-packing-list'); ?><br/>
		</div>
		<div style="float:right; text-align:left; width:25%; line-height: 20px;">
			<p style="font-size:<?php echo $content_size;?>px;margin-top:0px;">
				<?php
					$faddress=$this->wf_shipment_label_get_from_address();
					foreach ($faddress as $key => $value) {
						if (!empty($value)) {
							_e($value,'wf-woocommerce-packing-list');
							echo '<br>';
						}
					}
				?>
			</p>
		</div>
		<div style="clear:both;"></div>
	</header>
	<div>
		<div class="article" >
			<header style="height: 200px;">
				<div style="float:left; width: 49%;font-size:<?php echo $title_size;?>px;">
					<p style="margin-bottom: 0px;"><strong><?php _e('Billing address:', 'wf-woocommerce-packing-list'); ?></strong></p>
					<p style="font-size:<?php echo $content_size;?>px; line-height: 25px; margin-top: 5px;">
						<?php _e($order->get_formatted_billing_address() , 'wf-woocommerce-packing-list'); ?>
					</p>
					<div style="font-size:<?php echo $content_size;?>px;">
					<?php
						if ($order->billing_email){ ?>
						<p>
							<strong>
								<?php _e('Email:', 'wf-woocommerce-packing-list'); ?>
							</strong>
						<?php echo $order->billing_email; ?></p>
					<?php
						}
						if ($order->billing_phone): ?>
						<p><strong><?php _e('Tel:', 'wf-woocommerce-packing-list'); ?></strong> <?php
						echo $order->billing_phone; ?></p>
					<?php endif;
						if (((WC()->version < '2.7.0') ? $order->billing_VAT : get_post_meta($order_id,'_billing_VAT',true))): ?>
						<p><strong><?php _e('VAT:', 'wf-woocommerce-packing-list'); ?></strong> <?php echo ((WC()->version < '2.7.0') ? $order->billing_VAT : get_post_meta($order_id,'_billing_VAT',true)); ?></p>
					<?php endif;
						if (((WC()->version < '2.7.0') ? $order->billing_SSN : get_post_meta($order_id,'_billing_SSN',true))): ?>
							<p><strong><?php _e('SSN:', 'wf-woocommerce-packing-list'); ?></strong> <?php echo ((WC()->version < '2.7.0') ? $order->billing_SSN : get_post_meta($order_id,'_billing_SSN',true)); ?></p>
						<?php endif;
					 ?></div>
				</div>
				<div style="float:right; width: 49%;font-size:<?php echo $title_size;?>px;">
					<p style="margin-bottom: 0px;"><strong><?php _e('Shipping address:', 'wf-woocommerce-packing-list'); ?></strong></p>
					<div style="font-size:<?php echo $content_size;?>px; line-height: 25px; margin-top: 5px;">
						<?php
							if (get_post_meta($order_id, '_wcms_packages', true)) {
								$packages = get_post_meta($order_id, '_wcms_packages', true);
								foreach($packages as $package):
									echo '<p style="margin-top: 0px;">' . WC()->countries->get_formatted_address($package['full_address']) . '</p>';
								endforeach;
							} else {
						?>
						<p style="margin-top: 0px;"><?php echo $order->get_formatted_shipping_address(); ?></p>
						<?php }
							if (get_post_meta($order_id, '_tracking_provider', true)): ?>
								<p>
									<strong><?php _e('Tracking provider:', 'wf-woocommerce-packing-list'); ?></strong>
									<?php	echo get_post_meta($order_id, '_tracking_provider', true); ?>
								</p>
						<?php endif;
							if (get_post_meta($order_id, '_tracking_number', true)): ?>
								<p>
									<strong><?php _e('Tracking number:', 'wf-woocommerce-packing-list'); ?></strong>
									<?php echo get_post_meta($order_id, '_tracking_number', true); ?>
								</p><?php endif; ?>
					</div>
				</div>
				<div style="clear:both;"></div>
			</header>
			<div class="datagrid">
				<table style="width: 100%; border-collapse: collapse;border-bottom-width: thin; border-top-width: thick; border-top-color: #1e73be;" class="tableclass" cellspacing="0" cellpadding="0">
						<tr style="background-color:#1e73be; color: #FFFFFF;font-size:<?php echo $title_size;?>px;">
							<?php
								if(get_option('woocommerce_wf_attach_image_packinglist')=='Yes'){
									echo'<th scope="col" style="text-align:center; width:15%;border: 1px solid #1e73be; border-top-width: thin; padding: 5px;">';
									echo qtranxf_use($user_lang, 'Image', 'wf-woocommerce-packing-list'); echo '</th>';
								}
							?>
							<th scope="col" style="text-align:center; width:20%;border: 1px solid #1e73be; border-top-width: thin; padding: 5px;">
							<?php echo qtranxf_use($user_lang, 'SKU', 'wf-woocommerce-packing-list'); ?></th>
							<th scope="col" style="text-align:center; width:20%;border: 1px solid #1e73be; border-top-width: thin; padding: 5px;">
							<?php echo qtranxf_use($user_lang, '[:en]Product[:zh]产品[:hk]產品[:]', 'wf-woocommerce-packing-list'); ?></th>
							<th scope="col" style="text-align:center; width:10%;border: 1px solid #1e73be; border-top-width: thin; padding: 5px;">
							<?php echo qtranxf_use($user_lang, 'Quantity', 'wf-woocommerce-packing-list'); ?></th>
							<th scope="col" style="text-align:center; width:20%;border: 1px solid #1e73be; border-top-width: thin; padding: 5px;">
							<?php echo qtranxf_use($user_lang, 'Total Weight', 'wf-woocommerce-packing-list'); ?></th>
							<?php
								if(get_option('woocommerce_wf_attach_price_packinglist')=='Yes'){
									echo'<th scope="col" style="text-align:center; width:15%;border: 1px solid #1e73be; border-top-width: thin; padding: 5px;">';
									echo qtranxf_use($user_lang, 'Total Price', 'wf-woocommerce-packing-list'); echo '</th>';
								}
							?>
						</tr>
					<tbody>
						<?php echo qtranxf_use($user_lang, $this->woocommerce_packinglist_order_items_table($order_package)); ?>
					</tbody>
				</table>
			<div style="clear:both;"></div>
			</div>
			<div style="clear:both;"></div>
		</div>
	</div>
	<div style="clear:both;"></div>
</div>
