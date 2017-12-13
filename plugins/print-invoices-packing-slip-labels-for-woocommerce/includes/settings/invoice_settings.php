<div class="tabcontent">
	<h3 class="settings_headings"><?php	_e('Invoice Settings : ', 'wf-woocommerce-packing-list'); ?></h3>
	<div class="inside packinglist-printing-preview">
		<table class="form-table">
			<tr>
				<th></span><?php _e('Standard Templates', 'wf-woocommerce-packing-list'); ?></span></th>
				<td>
					<div class="theme-browser rendered" style="width:85%;">
						<div class="themes wp-clearfix">
							<?php  
							for ($i=1; get_option('wf_invoice_template_'.$i) !='';$i++)
							{
								if(get_option('wf_invoice_template_'.$i.'custom') == false && get_option('wf_invoice_active_key') === 'wf_invoice_template_'.$i)
									{  ?>

								<div class="theme" tabindex="0">
									<a href="#" style="color:white;">
										<div class="theme-screenshot" style="height:220px;">
											<img src="<?php echo WF_INVOICE_MAIN_ROOT_PATH.'assets/images/invoice'.$i.'.png'?>" alt=""> 
										</div>
										
										<span class="more-details" id="">Customize <br><small style="color:red;">(Pro version) </small></span></a>
										<h2 class="theme-name" id="" style="height:50%" >
											<?php 
											if(get_option('wf_invoice_active_key') === 'wf_invoice_template_'.$i)
											{
												echo '<p style="color:#26B99A;font-size:12px;font-weight:normal;" >Active</p> ';
											}
											else
												{ ?>
											<a class="btn btn-sm btn-info pull-right" href="<?php echo admin_url('admin.php?page=wf_woocommerce_packing_list&active_tab=invoice&theme=wf_invoice_template_'.$i) ?>">Activate</a>
											<?php }
											?>
											Invoice <?php echo $i; ?> </h2>
										</div>	
										<?php }
									}
									?>
									
									<?php  
									for ($i=1; get_option('wf_invoice_template_'.$i) !='';$i++)
									{
										if(get_option('wf_invoice_template_'.$i.'custom') == false && get_option('wf_invoice_active_key') != 'wf_invoice_template_'.$i)
											{ ?>
										<div class="theme" tabindex="0">
											<a href="#" style="color:white;">
												
												<div class="theme-screenshot" style="height:220px;">
													<img src="<?php echo WF_INVOICE_MAIN_ROOT_PATH.'assets/images/invoice'.$i.'.png'?>" alt=""> 
												</div>
												<span class="more-details" id="">Customize <br><small style="color:red;">(pro version) </small></span></a>
												<h2 class="theme-name" id="" style="height:50%" >
													<?php 
													if(get_option('wf_invoice_active_key') === 'wf_invoice_template_'.$i)
													{
														echo '<p style="color:#26B99A;font-size:12px;font-weight:normal;" >Active</p> ';
														
													}
													else
														{ ?>
													<a class="btn btn-sm btn-info pull-right" href="<?php echo admin_url('admin.php?page=wf_woocommerce_packing_list&active_tab=invoice&theme=wf_invoice_template_'.$i) ?>">Activate</a>
													<?php }
													?>
													Invoice <?php echo $i; ?> </h2>
												</div>	
												<?php }
											}
											?>
											
										</div>
									</div>
								</td>	
							</tr>
			<tr>
				<th> <label for="woocommerce_wf_packinglist_logo"><b><?php _e('Custom Logo', 'wf-woocommerce-packing-list'); ?></b></label></th>
				<td><input id="woocommerce_wf_packinglist_logo" type="text" size="36" name="woocommerce_wf_packinglist_logo" value="" />
					<input id="upload_image_button" type="button"  class="button button-primary" value="<?php _e('Upload Image', 'wf-woocommerce-packing-list'); ?>" /><br />
					<span class="description"><?php
						echo '<strong>' . __('Note:', 'wf-woocommerce-packing-list') . '</strong> ';
						echo __('Leave blank to not use a custom logo.', 'wf-woocommerce-packing-list');?>
					</span>
				</td>
			</tr>
			<tr>
				<th><span><?php _e('Use Order Number as Invoice Number', 'wf-woocommerce-packing-list'); ?></span></th>
				<td>
					<input type="checkbox" value="Yes" name="woocommerce_wf_invoice_as_ordernumber" class=""
					<?php if(get_option('woocommerce_wf_invoice_as_ordernumber')== "Yes") 
						_e('checked', 'wf-woocommerce-packing-list');					
					?> >
				</td>
			</tr>
			<tr class="invoice_hide">
				<th></span><?php _e('Invoice Start Number', 'wf-woocommerce-packing-list'); ?></span></th>
				<td>	
					<input type="number" min="0" name="woocommerce_wf_invoice_start_number" readonly class=""
					value="<?php echo stripslashes(get_option('woocommerce_wf_invoice_start_number')); ?>">
				</td>	
			</tr>
			<tr class="invoice_hide">
				<th><span><?php _e('Reset Invoice Number', 'wf-woocommerce-packing-list'); ?></span></th>
				<td><input type="checkbox"  name="woocommerce_wf_invoice_regenerate" class=""></td>
			</tr>
			<tr>
				<th></span><?php _e('Generate Invoice For', 'wf-woocommerce-packing-list'); ?></span></th>
				<td>	
					<select style="width:350px" class="wc-enhanced-select" id="order_status" data-placeholder='Choose Order Status' name="woocommerce_wf_generate_for_orderstatus[]" multiple="multiple">
						<?php
						$statuses = wc_get_order_statuses();
						foreach($statuses as $key =>$value) {			
							echo "<option value=$key"; if(in_array($key,$this->wf_generate_invoice_for)) echo "  selected"; echo ">$value</option>";
						} ?>
					</select>
				</td>	
			</tr>
		</table>	
	</div>
</div>