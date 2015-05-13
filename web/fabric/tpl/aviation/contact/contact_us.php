<div class="col-md-9">
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
			<div class="row">
				<div class="form-group row">
					<label class="col-md-3">Name</label>
					<div class="input-group col-md-9">
						<input class="form-control" name="name" value="<?php echo lc('uri')->post('name', ''); ?>">
					</div>
				</div>
				<div class="form-group row">
					<label class="col-md-3">Company</label>
					<div class="input-group col-md-9">
						<input class="form-control" name="company" value="<?php echo lc('uri')->post('company', ''); ?>">
					</div>
				</div>
				<div class="form-group row">
					<label class="col-md-3">Email</label>
					<div class="input-group col-md-9">
						<input class="form-control" name="email" value="<?php echo lc('uri')->post('email', ''); ?>">
					</div>
				</div>
				<div class="form-group row">
					<label class="col-md-3">Phone</label>
					<div class="input-group col-md-9">
						<input class="form-control" name="phone" value="<?php echo lc('uri')->post('phone', ''); ?>">
					</div>
				</div>
				<div class="form-group row">
					<label class="col-md-3">Fax</label>
					<div class="input-group col-md-9">
						<input class="form-control" name="fax" value="<?php echo lc('uri')->post('fax', ''); ?>">
					</div>
				</div>
				<div class="form-group row">
					<label class="col-md-3">Street Address</label>
					<div class="input-group col-md-9">
						<input class="form-control" name="address_line_1" value="<?php echo lc('uri')->post('street_address_line_1', ''); ?>"><br>
						<input class="form-control" style="margin-top:5px;" name="address_line_2" value="<?php echo lc('uri')->post('street_address_line_2', ''); ?>">

					</div>
				</div>
				<div class="form-group row">
					<label class="col-md-3">City</label>
					<div class="input-group col-md-9">
						<input class="form-control" name="city" value="<?php echo lc('uri')->post('city', ''); ?>">
					</div>
				</div>
				<div class="form-group row">
					<label class="col-md-3">State/Province</label>
					<div class="input-group col-md-9">
						<select class="form-control" size="1" name="state_id">
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
					</div>
				</div>
				<div class="form-group row">
					<label class="col-md-3">Country</label>
					<div class="input-group col-md-9">
						<select class="form-control" size="1" name="country_id">
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
						</select>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-md-3">Zip/Postal Code</label>
					<div class="input-group col-md-9">
						<input class="form-control" name="zip_code" value="<?php echo lc('uri')->post('zip_code', ''); ?>">
					</div>
				</div>
				<div class="form-group row">
					<label class="col-md-3"><em>detail your request...</em></label>
					<div class="input-group col-md-9">
						<textarea class="form-control" name="order_comments[]" rows="10" cols="60"></textarea>
					</div>
				</div>
				<div class="form-group">
					<input type="submit"  class='form-control btn btn-primary' name="submitted" value="Submit"/>
				</div>
			</div>
		</form>
	</div>
	<?php echo $ecom_content ?>
</div>