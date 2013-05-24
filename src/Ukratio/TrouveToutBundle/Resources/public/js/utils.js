function submitIfValid(name) {
	$('form').prepend('<input type="submit" name="' + name + '" id="submit_button"/>');
	$('#submit_button').click();
	$('#submit_button').remove();
}
