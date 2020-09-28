<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php do_action( 'wpo_wcpdf_before_document', $this->type, $this->order ); ?>
<table class="head container">
	<tr>
		<td class="header">
		<?php
		if( $this->has_header_logo() ) {
			?>
			<div style="width: 180px; height: auto;" class="logo"><?php $this->header_logo(); ?></div>
			<?php
		} else {
			echo $this->get_title();
		}
		?>
		<div class="shop-info">
			<div class="shop-name"><h3><?php $this->shop_name(); ?></h3></div>
			<div class="shop-address"><?php $this->shop_address(); ?></div>
			</td>
		</div>
		<td style="vertical-align: middle;" class="shop-info">
			<div style="text-align: center;margin-top: 30px;" class="invoice-number">
				<b><?php _e( 'Invoice Number:', 'woocommerce-pdf-invoices-packing-slips' ); ?></b><br>
				<span><?php $this->invoice_number(); ?></span>
			</div>
		</td>
	</tr>
</table>
<!-- username section -->
<table class="head container">
	<tr>
		<td>
			<div>
				<span style="background: black; color: white; padding: 5px 10px; display: inline-block;">Cliente: </span>
				<span style="padding: 5px 0px; display: inline-block;">
				<?php $this->custom_field('billing_first_name'); ?>
				<?php $this->custom_field('billing_last_name'); ?>
				</span>
			</div>
		</td>
		<td>
			<div>

				<?php
				ob_start();
				$this->custom_field('_billing_company' );
				$output = ob_get_contents();
				ob_end_clean();

				ob_start();
				$this->custom_field('_billing_wooccm11');
				$pk_new_rtn = ob_get_contents();
				ob_end_clean();
				if( !empty($pk_new_rtn) ){
					$output = str_replace($pk_new_rtn, ' ', $output);
					$output = str_replace('R.T.N.:', ' ', $output);
					$output = str_replace(',', ' ', $output);
				}
				if( !empty($output) ) :
				?>
				<span style="background: black; color: white; padding: 5px 10px; display: inline-block;">Empresa: </span>
				<span style="padding: 5px 0px; display: inline-block;">
				<?php echo $output; ?>
				</span>
				<?php endif; ?>
			</div>
		</td>
	</tr>
	<?php if( !empty($pk_new_rtn) ) : ?>
	<tr>
		<td>
			<div>
				<span style="background: black; color: white; padding: 5px 10px; display: inline-block;">R.T.N: </span>
				<span style="padding: 5px 0px; display: inline-block;">
				<?php echo $pk_new_rtn; ?>
				</span>
			</div>
		</td>
	</tr>
	<?php endif; ?>
</table>
<h1 class="document-type-label">
<?php if( $this->has_header_logo() ) echo $this->get_title(); ?>
</h1>
<?php do_action( 'wpo_wcpdf_before_order_details', $this->type, $this->order ); ?>
<!-- product details table starts -->
<table class="order-details custom">
	<thead>
		<tr>
			<th class="quantity"><?php _e('Cantidad', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			<th class="product"><?php _e('Descripción', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			<th class="price"><?php _e('Precio Unitario', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			<th class="price"><?php _e('Subtotal', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			<th class="discounts" style="font-size:10px;"><?php _e('Descuentos', 'woocommerce-pdf-invoices-packing-slips' ); ?><span style="font-size:8px;">y Rebajas Otorgados</span></th>
			<th class="price"><?php _e('Total', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$isv_percent = array();
		$items = $this->get_order_items(); if( sizeof( $items ) > 0 ) : foreach( $items as $item_id => $item ) : ?>
		<tr class="<?php echo apply_filters( 'wpo_wcpdf_item_row_class', $item_id, $this->type, $this->order, $item_id ); ?>">
			<td class="quantity"><?php echo $item['quantity']; ?></td>
			<td class="product">
				<?php $description_label = __( 'Description', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
				<span class="item-name"><?php echo $item['name']; ?></span>
				<?php do_action( 'wpo_wcpdf_before_item_meta', $this->type, $item, $this->order  ); ?>
				<span class="item-meta"><?php echo $item['meta']; ?></span>
				<dl class="meta">
					<?php $description_label = __( 'SKU', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
					<?php if( !empty( $item['sku'] ) ) : ?><dt class="sku"><?php _e( 'SKU:', 'woocommerce-pdf-invoices-packing-slips' ); ?></dt><dd class="sku"><?php echo $item['sku']; ?></dd><?php endif; ?>
					<?php if( !empty( $item['weight'] ) ) : ?><dt class="weight"><?php _e( 'Weight:', 'woocommerce-pdf-invoices-packing-slips' ); ?></dt><dd class="weight"><?php echo $item['weight']; ?><?php echo get_option('woocommerce_weight_unit'); ?></dd><?php endif; ?>
				</dl>
				<?php do_action( 'wpo_wcpdf_after_item_meta', $this->type, $item, $this->order  ); ?>
			</td>
			<td class="price">
				<?php


				$ex_price = ( preg_replace( '/[^.\d]/', '', $item['ex_single_price'] ) );

				// var_dump($ex_price);


				// $price = $product->get_price();
				// $r_p = $product->get_regular_price();
				// $product->get_sale_price();
				// $product->get_price();

				$regular_price = (int) $item['product']->regular_price;

				$r =  $item['calculated_tax_rates'];
				$r = intval(preg_replace('/[^0-9]+/', '', $r) , 10);
				$r = $r/100;

				// $ttd = '0.'.$r;
				// $ttd = (int) $ttd;
				// $decimal_percent = $r / 100;

				// $pk_price_nt = $regular_price / ( 1 - $decimal_percent );
				// $pk_sale_price = $regular_price - $pk_price_nt;

				// price before tax
				$tr = ($regular_price / (1 + $r)) * $r;
				$pk_price_only_price = $regular_price - $tr;
				$pk_price = $pk_price_only_price;
				echo $this->format_price( $pk_price_only_price );
				// echo $this->format_price( $ex_price );
				?>
			</td>
			<td class="price subtotal"><?php

				$price_subtotal =  $pk_price * $item['quantity'] ;
				// subtotal to use bottom row
				$pk_total_subtotal += $price_subtotal;


				echo $this->format_price( $price_subtotal );
				?>
			</td>
			<td class="price discounts">
				<?php



				$order_price_c = preg_replace( '/[^.\d]/', '', $item['line_subtotal'] );
				$line_total_c = preg_replace( '/[^.\d]/', '', $item['line_total'] );

				$total_discount = $order_price_c - $line_total_c;
				$sale_discount = $pk_price - $ex_price;

				if( isset($sale_discount) ){
					$qty = (int) $item['quantity'];
					$total_discount += ( $sale_discount * $qty );
				}

				// total discount to use in total row
				$pk_discount_totals += $total_discount;



				echo $this->format_price( $total_discount );
				?>
			</td>
			<td class="price total">
				<?php



				$total_c = $price_subtotal - $total_discount;
				// total of total to use on bottom total
				$pk_total_total += $total_c;

				echo $this->format_price( $total_c );
				 ?>

			<?php
				// calculation not included tax product price ammount.

				if( ! empty( $item['line_tax'] )){
					$line_tax = $item['line_tax'];
					$line_tax = preg_replace( '/[^.\d]/', '', $line_tax );
					$line_tax = (float) $line_tax;
					if( $line_tax == 0 ){
						$no_tax_isv_base += $total_c;
						$class_tax = $item['item']['tax_class'];

						if( ! array_key_exists($class_tax, $tax_base_no)) {
							$tax_base_no[$class_tax] = $total_c;
						}else {
							$tax_base_no[$class_tax] += $total_c;
						}
					}
				}
				// calculating tax base to add to bottom of product table.
				if( ! empty( $item['tax_rates'] )){
					$line_tax = $item['tax_rates'];
					$line_tax = preg_replace( '/[^.\d]/', '', $line_tax );
					$line_tax = (float) $line_tax;
					if( $line_tax ){
						// var_dump($line_tax);
						$array_key = 'percent_'.$line_tax;
						if( ! array_key_exists($array_key, $isv_percent)) {
							$isv_percent[$array_key] += $total_c;
						}else {
							$isv_percent[$array_key] += $total_c;
						}

						$array_key = 'percent_'.$line_tax;
						$class_tax = $item['item']['tax_class'];
						if( empty($class_tax) ){
							$class_tax = 'Isv Base Amount';
						}
						if( ! array_key_exists($class_tax, $tax_base_percent)) {
							$tax_base_percent[$class_tax] = array(
								'label'	 => $array_key,
								'value'	 => $total_c,
								'percent'	=> $line_tax,
								'class'		=> $class_tax
							);
						}else {
							$tax_base_percent[$class_tax]['value'] += $total_c;
						}
					}

				}
			?>
			</td>

			<!-- $int = wc_get_price_decimals(); -->
		</tr>
		<?php endforeach; ?>
		<!-- shipping row -->
		<tr>
			<td></td>
			<td>Envío</td>
			<td><?php $ship = $this->get_order_shipping('shipping_total');
				$ship_val = isset($ship['value'])?$ship['value']:0;
				$ship_tax = isset($ship['tax'])?$ship['tax']:0;
				$ship_val = preg_replace( '/[^.\d]/', '', $ship_val );
				$ship_tax = preg_replace( '/[^.\d]/', '', $ship_tax );
				$ship_no_tax = $ship_val - $ship_tax;
				$pk_total_subtotal += $ship_no_tax;
				echo $this->format_price ( $ship_no_tax );

			 ?></td>
			<td><?php echo $this->format_price( $ship_no_tax ); ?></td>
			<td><?php echo $this->format_price ( 0 ); ?></td>
			<td><?php
			$pk_total_total += $ship_no_tax;
			echo $this->format_price ( $ship_no_tax ); ?></td>
		</tr>
		<!-- total row of bottom total row total-->
		<tr>
			<td><b>Total</b></td>
			<td></td>
			<td></td>
			<td><b><?php



			echo $this->format_price( $pk_total_subtotal );
			?></b></td>
			<td><b><?php
			echo $this->format_price( $pk_discount_totals ); ?></b></td>
			<td><b><?php
			echo $this->format_price( $pk_total_total ); ?></b></td>
		</tr>
		<?php endif; ?>
	</tbody>
	<tfoot>
		<tr class="no-borders">
		<td class="no-borders" colspan="2">
				<div class="customer-notes">
					<?php do_action( 'wpo_wcpdf_before_customer_notes', $this->type, $this->order ); ?>
					<?php if ( $this->get_shipping_notes() ) : ?>
						<h3><?php _e( 'Customer Notes', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
						<?php $this->shipping_notes(); ?>
					<?php endif; ?>
					<?php do_action( 'wpo_wcpdf_after_customer_notes', $this->type, $this->order ); ?>
					<br />
					<span style="font-size: 8px;">N. CORRELATIVO DE ORDEN DE COMPRA EXENTA:</span><br />
					<span style="font-size: 8px;">N. CORRELATIVO DE CONSTANCIA DE REGISTRO EXONERADO:</span><br />
					<span style="font-size: 8px;">N. IDENTIFICATIVO DEL REGISTRO DE LA SAG:</span>
				</div>
			</td>
			<!-- SUBTOTAL STARTS -->
			<!-- IMPORTE EXENTO y EXONERADO -->
			<td class="no-borders" colspan="4">
				<table class="totals new">
					<tfoot>
					<?php
					$pk_final_total = $this->get_woocommerce_totals();
					$pk_final_total['cart_subtotal']['value'] = $this->format_price( $pk_total_total );

					// no tax isv base to the totals array

					$exent_val = $this->format_price( $no_tax_isv_base );
					$new_arr_1 = array(
						'no_tax_isv_base' =>
						array(
						'label' => "IMPORTE EXENTO:",
						'value' => $exent_val
					));
					// array_splice( $pk_final_total, 1, 0, $new_arr_1 );

					$exoner_val = $this->format_price(0 );
					$new_arr_2 = array(
						'no_tax_isv_base' =>
						array(
						'label' => "IMPORTE EXONERADO:",
						'value' => $val2
					));
					// array_splice( $pk_final_total, 1, 0, $exoner_val );

					// shipping calculation new
					$ship = $this->get_order_shipping('shipping_total');
					$ship_val = isset($ship['value'])?$ship['value']:0;
					$ship_tax = isset($ship['tax'])?$ship['tax']:0;
					$ship_val = preg_replace( '/[^.\d]/', '', $ship_val );
					$ship_tax = preg_replace( '/[^.\d]/', '', $ship_tax );
					$ship_no_tax = $ship_val - $ship_tax;
					$pk_total_subtotal += $ship_no_tax;

					// delete

					$shipping_percent = ( $ship_tax / $ship_no_tax ) * 100;


					$i = 2;

					// $pk_total_subtotal += $ship_no_tax;

					// Tax base adding to the bottom total subtotal array at table bottom.
					$tax_bases = array();
					$shipping_percent = ( 100 / $ship_no_tax ) * $ship_tax;
					foreach ( $tax_base_percent as $key => $value ) {
						$keys = preg_replace('/[0-9]+/', '', $key);
						$keys = str_replace('-', ' ', $keys );
						$v = preg_replace( '/[^.\d]/', '',  $value['value'] );

						if( strpos($key, 'exon') || strpos($key, 'impu') ){
							$new_tax_base_a = array(
								'base_value_'.$value['percent'].'_percent' =>
									array(
									'label' => "<span style='text-transform: capitalize;'>Exonerated ISV Base</span>",
									'value' => $v
								));
								array_splice( $tax, $i, 0, $new_tax_base_a );
						}else {
							if( $value['percent'] == $shipping_percent ){

								$v = $v + $ship_no_tax;

								$v = $this->format_price( $v );

								$new_tax_base_a = array(
								'base_value_'.$value['percent'].'_percent' =>
									array(
									'label' => "<span style='text-transform: capitalize;'>"."IMPORTE GRAVADO AL ".$value['percent']."%</span>",
									'value' => $v
								));
								//array_splice( $pk_final_total, $i, 0, $new_tax_base_a );
								$tax_bases += $new_tax_base_a;
							}else {
								$v = $this->format_price( $v );

								$new_tax_base_a = array(
								'base_value_'.$value['percent'].'_percent' =>
									array(
									'label' => "<span style='text-transform: capitalize;'>"."IMPORTE GRAVADO AL ".$value['percent']."%</span>",
									'value' => $v
								));
								// array_splice( $pk_final_total, $i, 0, $new_tax_base_a );
								$tax_bases += $new_tax_base_a;
							}
						}

						$i++;
					}
					// <!-- Adding shipping to taxbase of 15% only if no products don't have taxes -->
						if(!array_key_exists("Isv Base Amount", $tax_base_percent)){
							$value = array("label"=>"percent_15","value"=>$ship_no_tax, "percent"=>15, "class"=>"Isv Base Amount");
							$new_tax_base_a = array(
							'base_value_'.$value['percent'].'_percent' =>
								array(
								'label' => "<span style='text-transform: capitalize;'>"."IMPORTE GRAVADO AL ".$value['percent']."%</span>",
								'value' => $ship_no_tax
								));
							// array_splice( $pk_final_total, $i, 0, $new_tax_base_a );
							$tax_bases += $new_tax_base_a;
						}

					// base new new new
					// all tax information ( it is not base it is the tax ammount calculation  )
					$all_taxes = $this->get_order_taxes();

					// $i = 4;
					foreach ($all_taxes as $key => $value) {
						$all_tx_s = array();
						$v = preg_replace( '/[^.\d]/', '', $value['value'] );

						if( $v > 0 ) {
							// $v_n = $this->format_price( $v );
							$vv = $this->format_price($v);
							$all_tx_s[$value['label']] = array(
								'label'	=> $value['label'],
								'value'	=> $vv
							);

							$array_new_one[] = $all_tx_s;

							array_splice( $pk_final_total, $i, 0, $all_tx_s );

							$i++;
						}
					}




					// unset shipping
					unset( $pk_final_total['shipping'] );
					unset( $pk_final_total['payment_method'] );
					unset( $pk_final_total['discount'] );
					// Grand total fixing removing unusual texts.
					$order_pay_total = $pk_final_total['order_total']['value'];
					// Grand total unset
					// unset( $pk_final_total['order_total'] );

					$tot_arr = explode('(', $order_pay_total);
					// new grand total
					$pk_final_total['order_total']['label'] = '<b style="text-transform: uppercase;">TOTAL A PAGAR:</b>';
					$pk_final_total['order_total']['value'] = '<b>'.$tot_arr[0].'</b>';
					?>


					 <!-- base amounts and taxes row -->
					 <tr>
						 <td class="no-borders"></td>
						 <th class="description_new">Subtotal</th>
						 <td style="text-align: center;" class="price_new"><span class="totals-price"><?php echo $pk_final_total['cart_subtotal']['value'];?></span></td>
					 </tr>
					 <tr>
						<td class="no-borders"></td>
						<th class="description_new">IMPORTE EXENTO</th>
						<td style="text-align: center;" class="price_new"><span class="totals-price"><?php echo $exent_val;?></span></td>
					</tr>
					<tr>
					 <td class="no-borders"></td>
					 <th class="description_new">IMPORTE EXONERADO</th>
					 <td style="text-align: center;" class="price_new"><span class="totals-price"><?php echo $exoner_val;?></span></td>
				 </tr>
				 <!-- BASE AMOUNTS -->
				 <!-- Guarantee ISV 18 and 15 bases are always populated -->
				 <?php
				 	if(!array_key_exists("base_value_18_percent", $tax_bases)){
						$tax_bases["base_value_18_percent"] = array(
							"label" => "IMPORTE GRAVADO AL 18%",
              "value" => "L0.00");
					};
					if(!array_key_exists("base_value_15_percent", $tax_bases)){
						$tax_bases["base_value_15_percent"] = array(
							"label" => "IMPORTE GRAVADO AL 15%",
							"value" => "L0.00");
					};

				 ?>
				 <?php foreach($tax_bases as $key => $base): ?>
					<tr>
						<td class="no-borders"></td>
						<th class="description_new"><?php echo $base['label']; ?></th>
						<td style="text-align: center;" class="price_new"><span class="totals-price"><?php echo $base['value']?>
					</tr>
				<?php endforeach; ?>

				 		<?php unset($pk_final_total['cart_subtotal']) //remove subtotal ?>
						<?php unset($pk_final_total['order_total']) //remove total ?>
						<?php foreach( $pk_final_total as $key => $total ) : ?>
							<tr class="<?php echo $key.'new'; ?>">
								<td class="no-borders"></td>
								<th class="description_new"><?php echo $total['label']; ?></th>
								<td style="text-align: center;" class="price_new"><span class="totals-price"><?php echo $total['value'];
								 ?></span></td>
							</tr>
						<?php endforeach; ?>
						<tr>
						 <td class="no-borders"></td>
						 <th class="description_new"><b style="text-transform: uppercase;">TOTAL A PAGAR:</b></th>
						 <td style="text-align: center;" class="price_new"><span class="totals-price"><?php echo '<b>'.$tot_arr[0].'</b>'?></span></td>
					 </tr>
					</tfoot>
				</table>
			</td>
			<!-- SUBTOTAL ENDS -->
		</tr>
	</tfoot>
</table>
<!-- product details table ends -->
<!-- descriptioin invoice starts  -->
<?php do_action( 'wpo_wcpdf_after_document_label', $this->type, $this->order ); ?>
<!--<div class="bottom-spacer"></div> -->
<?php do_action( 'wpo_wcpdf_after_order_details', $this->type, $this->order ); ?>

<?php if ( $this->get_footer() ): ?>
<div id="footer">
	<?php $this->footer(); ?>
</div><!-- #letter-footer -->
<?php endif; ?>
<?php do_action( 'wpo_wcpdf_after_document', $this->type, $this->order ); ?>
<pre>
	<?php
		// var_dump( $this->get_order_taxes() );
		// var_dump( $this->get_order_taxes() );
		// var_dump( $array_new_one );
		var_dump( $pk_final_total);
		var_dump($tax_bases);
		// var_dump( $pk_final_total['order_total']['value'] );
		var_dump( $ship );
		var_dump($shipping_percent);

		// var_dump( $item['item']['tax_class'] );
		// var_dump( $r );
		// var_dump( $items[58]['product']['data']['regular_price'] );
		// var_dump( $items[60]['product']->regular_price );
		// var_dump( $items );

		var_dump($tax_base_percent);

	 ?>
</pre>
