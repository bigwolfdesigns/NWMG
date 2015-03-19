<?php
echo $this->grab('form');
?>
<div class="tabbable" style="margin-bottom: 18px;">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#order_tab" data-toggle="tab">Orders</a></li>
		<li class=""><a href="#contact_tab" data-toggle="tab">Contacts</a></li>
	</ul>
	<div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
		<div class="tab-pane active" id="order_tab">
			<?php
			echo $this->grab('list_table', array('rows' => $orders, '_config' => $order_config,'class_key'=>'order'));
			?>
		</div>
		<div class="tab-pane" id="contact_tab">
			<?php
			echo $this->grab('list_table', array('rows' => $contacts, '_config' => $contact_config,'class_key'=>'contact'));
			?>
		</div>
	</div>
</div>​
<script>
//	$('#tab2').load('/status.html');
//$('#tab3').load('/settings.html');
//$('#tab4').load('/help.html');
</script>