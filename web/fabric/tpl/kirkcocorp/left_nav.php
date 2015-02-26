<div id="left-navigation">
	<?php
	foreach($categories as $category){
		$main_category_alias = $category['alias'];
		$main_category_name	 = $category['name'];
		$main_category_desc	 = $category['description'];
		$sub_categories		 = $category['sub_categories']
		?>
		<span class="left-navgrey">
			<span class="left-header">
				<a href="/<?php echo $main_category_alias; ?>.html"><?php echo $main_category_name; ?></a>
			</span>
			<img alt="" src="/images/left-nav-divider(btqr72).gif" width="200" height="3" class="left-navigation-divider" />
			<?php
			if(trim($main_category_desc) !== ''){
				?>
				<p><?php echo $main_category_desc; ?></p>
				<?php
			}
			if(is_array($sub_categories) && !empty($sub_categories)){
				?>
				<ul class="nav-links">
					<?php
					foreach($sub_categories as $sub_category){
						$sub_category_alias	 = $sub_category['alias'];
						$sub_category_name	 = $sub_category['name'];
						?>
						<li>
							<a href="/<?php echo $main_category_alias.'/'.$sub_category_alias ?>.html"><?php echo $sub_category_name; ?></a>
						</li>
						<?php
					}
					?>
				</ul>
			<?php } ?>

		</span>
	<?php } ?>
	<span class="left-navgrey">
		<span class="left-header"><a href="/products/">Product Education</a></span>
		<img alt="" src="/images/left-nav-divider(btqr72).gif" width="200" height="3" class="left-navigation-divider" />
		<ul class="nav-links">
			<li><a href="/products/single-component-systems/">Single Component Systems</a></li>
			<li><a href="/products/two-component-systems/">Two Component Systems</a></li>
			<li><a href="/products/spraying-and-coating/">Spraying and Coating</a></li>
			<li><a href="/products/hot-melt-adhesive-technology/">Hot Melt Adhesive Technology</a></li>
			<li><a href="/products/cold-glue-adhesive-systems/">Cold Glue Adhesive Systems</a></li>
		</ul>
	</span>
	<span class="left-navgrey">
		<span class="left-header"><a href="/services/">Training &amp; Services</a></span>
		<img alt="" src="/images/left-nav-divider(btqr72).gif" width="200" height="3" class="left-navigation-divider" />
		<p>Factory Trained Service Assistance, Training and Repairs.</p>
		<ul class="nav-links">
			<li><a href="/services/in-field-installation/">In Field Installation</a></li>
			<li><a href="/services/training-and-education/">Training &amp; Education</a></li>
			<li><a href="/services/rebuild-and-repair/">Rebuild &amp; Repair </a></li>
		</ul>

	</span>
	<span class="left-navwhite">
		<span class="left-header"><a href="/approved-vendors/">Approved Vendors</a></span>
		<img alt="" src="/images/left-nav-divider(btqr72).gif" width="200" height="3" class="left-navigation-divider" />
		<p>Leading Manufacturers of Hot and Cold, Single or Plural Component Adhesive Metering and Dispensing Units.</p>
		<ul class="nav-links">
			<li><a href="/approved-vendors/hilger-u-kern-dopag-group/">DOPAG Group</a></li>
			<li><a href="/approved-vendors/wagner-group/">Wagner Group</a></li>
			<li><a href="/approved-vendors/walther-pilot/">Walther Pilot</a></li>
			<li><a href="/approved-vendors/itw-dynatec/">ITW Dynatec</a></li>
			<li><a href="/approved-vendors/pam-fastening-technology/">PAM Fastening</a></li>
			<li><a href="/approved-vendors/graco/">Graco</a></li>
			<li><a href="/approved-vendors/abb-robotics/">ABB Robotics</a></li>
		</ul>
	</span>
	<span class="left-navwhite">
		<span class="left-header"><a href="/industries-served/">Our Industries</a></span>
		<img alt="" src="/images/left-nav-divider(btqr72).gif" width="200" height="3" class="left-navigation-divider" />
		<div class="justified">
			<p>We have application experience in most all manufacturing fields. Success in serving the industrial, automotive, medical, electronics and other manufacturing markets with precision metering mixing and dispensing equipment.</p>
			<p>Whether you need a consistent bond, accurate electronic encapsulation, proportioning or transferring of adhesives, sealants or lubricants, or metering and dispensing of hot melt materials, our equipment offers dependable and lasting performance!</p>
		</div>
		<ul class="nav-links">
			<li><a href="/industries-served/">Industries &amp; Case Studies</a></li>
		</ul>
	</span>
</div>