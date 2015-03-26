$(function (){
	$('.top-navigation li').click(function (){
		redirect($(this).children('a').eq(0).attr('href'));
	});
});