$(document).ready(function() {
	language = "fr";
	var caractsManager;
	var categoriesManager;
	var isResearch = $('#TrouveTout_Research_name').length > 0; //TOSEE


	if (loggedUser || isResearch || isTutorial) {
		caractsManager = new CaractsManager();
		caractsManager.addButtonsForDynamicForms();

		categoriesManager = new CategoriesManager();
		categoriesManager.addButtonsForDynamicForms();
	}

	$(':input').on('change', function (event) {
		enableSave();
	});

	if (!isTutorial) {
		disableSave();
	} else {
		enableSave();
	}
});
