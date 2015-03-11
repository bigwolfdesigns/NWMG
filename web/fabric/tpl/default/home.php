<link href="/css/coming_soon/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/jquery/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="/js/coming_soon/cufon-yui.js"></script>
<script type="text/javascript" src="/js/coming_soon/Abraham_Lincoln.font.js"></script>
<script type="text/javascript" src="/js/coming_soon/Inspiration.font.js"></script>
<script type="text/javascript" src="/js/coming_soon/Museo_Slab.font.js"></script>
<script type="text/javascript">
	Cufon.replace('.logo h1', {
		fontFamily: 'Inspiration'
	});
	Cufon.replace('.logo h2', {
		fontFamily: 'Museo Slab 100'
	});
	Cufon.replace('.logo h2 span', {
		fontFamily: 'Abraham Lincoln'
	});
	Cufon.replace('p.big_text, p.small_text', {
		fontFamily: 'Museo Slab 100'
	});
	Cufon.replace('p.big_text strong, p.small_text strong', {
		fontFamily: 'Museo Slab 700'
	});
</script>


</head>
<body>
	<div id="transy">
	</div>
	<div id="wrapper">
		<div class="logo">
			<h1>Maxson Doors</h1>
			<h2>
				<strong class="one"></strong>The Door <span>&amp;</span> Loading Dock Equipment Specialist<strong class="two"></strong>
			</h2>
		</div>
		<div class="content">
			<?php if(isset($message)&&$message!=''){
				?>
				<p class="big_text"><?php echo $message?></p>
				<?php
			}
			?>
			<p class="big_text"><strong>we are working on something</strong> very interesting!</p>
			<p class="small_text"><strong>be notified.</strong> we just need your email address.</p>
			<div class="form">
				<div class="field_content">
					<form action="<?php echo $coming_soon_url?>" method="POST"/>
					<input name="email" class="field" type="text"  />
					<input name="coming_soon" class="submit" type="submit" value="Submit" />
					</form>
				</div>
			</div>
			<div class="clear"></div>
			<ul class="social">
				<li class="pinterest"><a href="#"></a></li>
				<li class="instagram"><a href="#"></a></li>
				<li class="twitter"><a href="#"></a></li>
				<li class="facebook"><a href="#"></a></li>
			</ul>
		</div>
	</div>