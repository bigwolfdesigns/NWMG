<?php
if(isset($featured_products)&&is_array($featured_products)&&!empty($featured_products)){
	?>
	<link href="/css/responsive-slides/responsive-slides.css" rel="stylesheet"  type="text/css"/>
	<script src="/js/responsive-slides/responsive-slides.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(function (){
			$(".featured_products").responsiveSlides({
				speed:1000,
				pause:true
			});
		});
	</script>
	<div class='featured_products_container'>
		<ul class="featured_products">
			<?php
			foreach($featured_products as $featured_product){
				$img_url = $featured_product['image'];
				?>
				<li><img src="<?php echo $img_url ?>"/></li><?php
			}
			?>
		</ul>
	</div>
	<?php
}