function filter_options(component, type, reference_id){
	$('keywords').value = component ;
	$('type_id').value = type ;
	$('item_id').value = reference_id ;
	
	$('adminForm').submit();
}
function filter_user(user_id){
	$('user_id').value = user_id ;
	
	$('adminForm').submit();
}

jQuery(document).ready(function() {
  jQuery("time.timeago").timeago();
});