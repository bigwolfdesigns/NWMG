var sort_top_menu_link={};
$(function (){
//	$('#top-menu-edit-container').hide();
	sort_top_menu_link=$('#sort-top-menu-links').sortable({
		update: update_top_menu_links
	});
	$('body').on('click', '.edit-top-nav', nav_edit_function);

	$('.top-menu-edit-attr').keyup(attribute_change_function);
	$('.top-menu-edit-attr').click(attribute_change_function);
	$('.top-menu-edit-attr').change(attribute_change_function);
});
var update_top_menu_links=function (event, ui){
	$("input[name='top_menu_json']").val(JSON.stringify($(this).sortable('toArray')));
};
var nav_edit_function=function (){
	//get the id..
	var sort_id=parseInt($(this).attr('id').replace('top_sort_', ''), 10)-1;
	//get those values from the javascript array produced
	var info={};
	if(typeof top_menu_links_json[sort_id]!=='undefined'){
		info=top_menu_links_json[sort_id];
	}else if(sort_id<0){
		//we got a new one..
		sort_id=top_menu_links_json.length;
		info={
			page: '',
			name: 'Link',
			sort: (sort_id+1),
			category_id: ''
		};
		top_menu_links_json[sort_id]=info;
		// now that the array is populated create the new "Button"
		var new_span=$('<span />').addClass("btn btn-primary edit-nav").attr('id', "top_sort_"+(sort_id+1)).html('Link');
		$('#top_sort_0').before(new_span);
	}
	//populate the editing fields for this link
	//slide down the editing fields
	$('#top-menu-edit-name').data('id', sort_id).val(info.name);
	$('#top-menu-edit-page').data('id', sort_id).val(info.page);
	$('#top-menu-edit-category-id').data('id', sort_id).val(info.category_id);
	$('#top-menu-edit-delete').data('id', sort_id);
//	$('#top-menu-edit-container').css('display', 'block');
};
var attribute_change_function=function (){
	var attr=$(this).data('attribute');
	var sort_id=$(this).data('id');
	var new_attr=$(this).val();
	var doit=true;
	if(attr==='delete'){
		doit=confirm("Are you sure you want to delete this Link?");
	}
	if(typeof top_menu_links_json[sort_id]!=='undefined'){
		if(attr==='delete'&&doit){
			top_menu_links_json.splice(sort_id, 1);

		}else{
			top_menu_links_json[sort_id][attr]=new_attr;
		}
	}
	if(attr==='name'){
		$('#top_sort_'+(sort_id+1)).html(new_attr);
	}else if(attr==='delete'&&doit){
		$('#top_sort_'+(sort_id+1)).remove();
//		$('#top-menu-edit-container').slideUp();
	}
	$("input[name='edit_top_menu_json']").val(JSON.stringify(top_menu_links_json));
	$("input[name='edit_top_menu_edit_submit']").show();
};






var sort_bottom_menu_link={};
$(function (){
	sort_bottom_menu_link=$('#sort-bottom-menu-links').sortable({
		update: update_bottom_menu_links
	});
	$('body').on('click', '.edit-bottom-nav', bottom_nav_edit_function);

	$('.bottom-menu-edit-attr').keyup(bottom_attribute_change_function);
	$('.bottom-menu-edit-attr').click(bottom_attribute_change_function);
});
var update_bottom_menu_links=function (event, ui){
	$("input[name='bottom_menu_json']").val(JSON.stringify($(this).sortable('toArray')));
};
var bottom_nav_edit_function=function (){
	//get the id..
	var sort_id=parseInt($(this).attr('id').replace('bottom_sort_', ''), 10)-1;
	//get those values from the javascript array produced
	var info={};
	if(typeof bottom_menu_links_json[sort_id]!=='undefined'){
		info=bottom_menu_links_json[sort_id];
	}else if(sort_id<0){
		//we got a new one..
		sort_id=bottom_menu_links_json.length;
		info={
			page: '',
			name: 'Link',
			sort: (sort_id+1)
		};
		bottom_menu_links_json[sort_id]=info;
		// now that the array is populated create the new "Button"
		var new_span=$('<span />').addClass("btn btn-primary edit-nav").attr('id', "bottom_sort_"+(sort_id+1)).html('Link');
		$('#bottom_sort_0').before(new_span);
	}
	//populate the editing fields for this link
	//slide down the editing fields
	$('#bottom-menu-edit-name').data('id', sort_id).val(info.name);
	$('#bottom-menu-edit-page').data('id', sort_id).val(info.page);
	$('#bottom-menu-edit-delete').data('id', sort_id);
//	console.log('ye aimhere');
//	$('#bottom-menu-edit-container').show();
};
var bottom_attribute_change_function=function (){
	var attr=$(this).data('attribute');
	var sort_id=$(this).data('id');
	var new_attr=$(this).val();
	var doit=true;
	if(attr==='delete'){
		doit=confirm("Are you sure you want to delete this Link?");
	}
	if(typeof bottom_menu_links_json[sort_id]!=='undefined'){
		if(attr==='delete'&&doit){
			bottom_menu_links_json.splice(sort_id, 1);

		}else{
			bottom_menu_links_json[sort_id][attr]=new_attr;
		}
	}
	if(attr==='name'){
		$('#bottom_sort_'+(sort_id+1)).html(new_attr);
	}else if(attr==='delete'&&doit){
		$('#bottom_sort_'+(sort_id+1)).remove();
//		$('#bottom-menu-edit-container').slideUp();
	}
	$("input[name='edit_bottom_menu_json']").val(JSON.stringify(bottom_menu_links_json));
	$("input[name='edit_bottom_menu_edit_submit']").show();
};