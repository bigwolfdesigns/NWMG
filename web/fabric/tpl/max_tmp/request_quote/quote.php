<h1>Request a Quote</h1>
<h2>Your Information <a style="font-size:10px;text-decoration:none;" href="/privacy-policy/" target="_new">(Privacy Policy)</a></h2>
<div id="siteform">
	<?php
	if(is_array($errors) && !empty($errors)){
		?>
		<div class="form-errors">
			<ul><?php
				foreach($errors as $error){
					?>
					<li><?php echo $error ?></li>
				<?php } ?>
			</ul>
		</div><?php
	}
	?>
	<form action="<?php echo lc('uri')->get_uri(); ?>" method="post">
		<input type='hidden' value='1' name='order_type_id' />
		<table border="0" cellspacing="4" cellpadding="4">
			<tbody>
				<tr>
					<td class="required" valign="top">Name</td>
					<td class="input" valign="top"><input name="name" value="<?php echo lc('uri')->post('name', ''); ?>"></td>
				</tr>
				<tr>
					<td class="required" valign="top">Company</td>
					<td class="input" valign="top"><input name="company" value="<?php echo lc('uri')->post('company', ''); ?>"></td>
				</tr>
				<tr>
					<td class="required" valign="top">Email</td>
					<td class="input" valign="top"><input name="email" value="<?php echo lc('uri')->post('email', ''); ?>"></td>
				</tr>
				<tr>
					<td class="required" valign="top">Phone</td>
					<td class="input" valign="top"><input name="phone" value="<?php echo lc('uri')->post('phone', ''); ?>"></td>
				</tr>
				<tr>
					<td class="" valign="top">Fax</td>
					<td class="input" valign="top"><input name="fax" value="<?php echo lc('uri')->post('fax', ''); ?>"></td>
				</tr>
				<tr>
					<td class="" valign="top">Street Address</td>
					<td class="input" valign="top">
						<input name="address_line_1" value="<?php echo lc('uri')->post('street_address_line_1', ''); ?>"><br>
						<input style="margin-top:5px;" name="address_line_2" value="<?php echo lc('uri')->post('street_address_line_2', ''); ?>">
					</td>
				</tr>
				<tr>
					<td class="required" valign="top">City</td>
					<td class="input" valign="top"><input name="city" value="<?php echo lc('uri')->post('city', ''); ?>"></td>
				</tr>
				<tr>
					<td class="required" valign="top">State/Province</td>
					<td class="input" valign="top">
						<select size="1" name="state_id">
							<option value="">Please Select</option>
							<?php
							$state_id = lc('uri')->post('state_id', '');
							foreach($states as $sid => $state){
								$selected = "";
								if($state_id == $sid){
									$selected = "SELECTED";
								}
								?><option value="<?php echo $sid ?>" <?php echo $selected ?>><?php echo $state['name'] ?></option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="required" valign="top">Country</td>
					<td class="input" valign="top">
						<select size="1" name="country_id">
							<option value="">Please Select</option>
							<?php
							$country_id = lc('uri')->post('country_id', 181);
							foreach($countries as $cid => $country){
								$selected = "";
								if($country_id == $cid){
									$selected = "SELECTED";
								}
								?><option value="<?php echo $cid ?>"<?php echo $selected ?>><?php echo $country['name'] ?></option>
								<?php
							}
							?>
						</select></td>
				</tr>
				<tr>
					<td class="" valign="top">Zip/Postal Code</td>
					<td class="input" valign="top">
						<input name="zip_code" value="<?php echo lc('uri')->post('zip_code', ''); ?>">
					</td>
				</tr>
<!--				<tr>
					<td class="" valign="top"></td>
					<td class="input" valign="top">
						<input type="submit" name="submit" value="Submit"/>
					</td>
				</tr>-->
			</tbody>
		</table>
		<h2>Product Information</h2>
		<table border="0" cellspacing="4" cellpadding="4">
			<tbody>
				<tr>
					<td class="required" valign="top">Which Product?</td>
					<td class="input" valign="top">
						<select name='products[]' >
							<option value="">Please Select</option>
							<?php
							foreach($products as $product_id => $product_name){
								?>
								<option value="<?php echo $product_id ?>"><?php echo $product_name ?></option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="required" valign="top">How Many?</td>
					<td class="input" valign="top"><input type="text" name="qtys[]" id="quantity" value=""></td>
				</tr>
	<!--			<tr>
					<td class="required" valign="top">Product Use?</td>
					<td class="input" valign="top">
						<select name="product_use">
							<option value="">Please Select</option>
							<option value="OEM">OEM Component</option>
							<option value="MRO">Replacement Unit (MRO)</option>
							<option value="Other">Other</option>
						</select></td>
				</tr>-->
	<!--			<tr>
					<td colspan="2" valign="top">
	
						<em>also send me more information on...</em><br>
	
						<input name="moreinfo[]" type="checkbox" value="Single Component"> Single Component Systems<br>
						<input name="moreinfo[]" type="checkbox" value="Two Component"> Two and Multi Component Systems<br>
						<input name="moreinfo[]" type="checkbox" value="Spraying and Coating"> Spraying and Coating/Laminating<br>
						<input name="moreinfo[]" type="checkbox" value="Hot Melt"> Hot Melt Adhesives<br>
						<input name="moreinfo[]" type="checkbox" value="Cold Adhesives"> Cold Adhesives<br>
	
					</td>
				</tr>-->

				<tr>
					<td colspan="2" valign="top"><br>
						<em>detail your request...</em><br>
						<textarea name="order_comments[]" rows="10" cols="60"></textarea>
					</td>

				</tr>
	<!--			<tr>
					<td class="input" valign="top">
						<span class="required">Check Code &gt;&gt;<br>
							<input type="text" name="captcha" size="20" maxlength="10"><input type="hidden" name="submit" value="b.jpg"></span></td>
					<td valign="top">
						<img src="/captcha/b.jpg" alt="captcha image" style="border:2px solid #333333;">
					</td>
				</tr>-->
				<tr>
					<td colspan="2" style="padding-top:20px;border-top:2px dotted #ebebeb;">
						<button type="submit"><img src="/images/send-button.png"></button>
					</td>
				</tr>

			</tbody>
		</table>
	</form>
	<?php echo $ecom_content ?>
</div>