<h1>Contact Us</h1>
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
		<input type="hidden" name="form" value="Contact Us Form">
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
				<tr>
					<td class="" valign="top">Comments</td>
					<td class="input" valign="top">
						<textarea name="comment" value="<?php echo lc('uri')->post('comment', ''); ?>"></textarea>
					</td>
				</tr>
				<tr>
					<td class="" valign="top"></td>
					<td class="input" valign="top">
						<input type="submit" name="submit" value="Submit"/>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	<?php echo $ecom_content ?>
</div>