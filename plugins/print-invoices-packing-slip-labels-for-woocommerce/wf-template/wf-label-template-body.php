<div style="<?php 
				$var=$this->wf_shipment_label_get_label_size();
				$size_factor;
				if($var==1) {
					_e('width:384px;','wf-woocommerce-packing-list');
					$size_factor = 0.75;
					$to_size_factor = 0.5;
				} else {
					_e('width:100%;','wf-woocommerce-packing-list');
					$size_factor = 1;
					$to_size_factor = 1;
				}
				$faddress=$this->wf_shipment_label_get_from_address();
				?> "><header >
				<a class="print" href="#" onclick="window.print()" ><?php _e('Print','wf-woocommerce-packing-list');?></a>
		<div style="float:left; width:49%; text-align:right; margin: 10px 20px 0 0;font-size:<?php echo $heading_size;?>px;"><strong>
			<?php echo $this->wf_packinglist_get_companyname();?></strong><br/>
		</div>
		<div style="clear:both;"></div>
	</header>
	<div >
		<div class="article" >
			<header style="height: <?php echo 180 * $size_factor; ?>px;">
				<div style="width: %;float:right;font-size:<?php echo $title_size * $size_factor; ?>px;line-height: <?php echo 15 * $size_factor; ?>px;">
					<?php $orderdetails = $this->wf_packinglist_get_table_content($order, $order_package); ?>
					<div>
						<table style="font-size: <?php echo $content_size * $size_factor; ?>px;">
						<?php if(!empty($orderdetails)) { ?>
							<tr>
								<td ><?php _e('Order Number','wf-woocommerce-packing-list');?></td>
								<td> : </td>
								<td><strong><?php _e($orderdetails['order_id'],'wf-woocommerce-packing-list');?></strong></td>
							</tr>
							<tr>
								<td><?php _e('Weight','wf-woocommerce-packing-list');?></td>
								<td> : </td>
								<td><strong><?php _e($orderdetails['weight'],'wf-woocommerce-packing-list');?></strong></td>
							</tr>
						<?php } if(key_exists('ship_date',$order_additional_information)) { ?>
								<tr>
									<td><?php _e('Ship Date','wf-woocommerce-packing-list');?></td>
									<td> : </td>
									<td><strong><?php _e($order_additional_information['ship_date'],'wf-woocommerce-packing-list');?></strong></td>
								</tr>
							<?php } ?>
						</table>
					</div>
				</div>
				<div style="float:left; width:49%; margin-top: 10px; font-size:<?php echo $title_size * $size_factor; ?>px;">
					<div style="padding-bottom:4px;" ><strong><?php _e('FROM:','wf-woocommerce-packing-list');?></strong></div>
					<div style="font-size:<?php echo $content_size * $size_factor; ?>px; line-height: <?php echo 20 * $size_factor; ?>px;">
						<?php 
							$faddress=$this->wf_shipment_label_get_from_address();
							foreach ($faddress as $key => $value) {
								if (!empty($value)) {
									_e($value,'wf-woocommerce-packing-list');
									echo '<br>';
								}
							}
						?>
					</div>
				</div>
				<div style="clear:both;"></div>
			</header>
			<div style="width: 100%;font-size: <?php echo $to_title_size * $to_size_factor; ?>px;margin-left: 20%;">
				<div><strong><?php _e('TO:','wf-woocommerce-packing-list');?></strong></div>
				<div style="font-size: <?php echo $to_content_size * $to_size_factor; ?>px;line-height: <?php echo 35 * $to_size_factor; ?>px;">
				<?php 
					$this->wf_shipment_label_get_to_address($order);
				?>
				</div>
				</div>
			<div class="datagrid">
				<div style="clear:both;"></div>
			</div>
			<div style="clear:both;"></div>
		</div>
	</div>
<div style="clear:both;"></div>
</div>
