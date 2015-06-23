function submitIfValid(name) {
	$('form').prepend('<input type="submit" name="' + name + '" id="submit_button"/>');
	$('#submit_button').click();
	$('#submit_button').remove(); //TODO see why
}

function enableSave() {
	$('[id=save]').removeAttr('disabled');
	$('[id=reload]').removeAttr('disabled');
}

function disableSave() {
	$('[id=save]').attr('disabled', 'true');
	$('[id=reload]').attr('disabled', 'true');
}

//TODO get transTab by phpTojs
function createTransTab() {
	var transTab = new Array("fr", "en");
	transTab["fr"] = {"other":"autre",
					  "plop":"plop",
					  "picture":"image",
					  "caract.reload":"Recharger la/les liste(s) déroulante(s)",
					  "caract.upload_picture":"Téléverser l’image"};
	return transTab;
}

function translate(sentence, lang) {
	var transTab = createTransTab(); //TODO change for static variable
	if(lang in transTab) {
		if(sentence in transTab[lang]) {
			return transTab[lang][sentence];
		} else {
			return sentence;
		}
	} else {
		alert("language unknow")
	}
}

