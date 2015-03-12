$(function (){
	$('.home_page_product_selector').change(function (){
		var val=$(this).val();
		if(val!==''){
			redirect(val);
		}
	});
});