function autocomplet() {
	var min_length = 1; 
	var keyword = $('#q').val();
	if (keyword.length >= min_length) {
		$.ajax({
			url: 'ajax_refresh.php',
			type: 'POST',
			data: {keyword:keyword},
			success:function(data){
				$('#suggest_list_id').show();
				$('#suggest_list_id').html(data);
			}
		});
	} else {
		$('#suggest_list_id').hide();
	}
}

function set_item(item) {
	
	$('#q').val(item);
	$('#suggest_list_id').hide();
	$('#myForm').submit();
}
