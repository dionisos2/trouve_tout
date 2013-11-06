function submitIfValid(name) {
	$('form').prepend('<input type="submit" name="' + name + '" id="submit_button"/>');
	$('#submit_button').click();
	$('#submit_button').remove();
}

function enableSave() {
	$('[id=save]').removeAttr('disabled');
}

function disableSave() {
	$('[id=save]').attr('disabled', 'true');
}

