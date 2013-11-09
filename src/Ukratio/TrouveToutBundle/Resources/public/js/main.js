$(document).ready(function() {
	var caractsManager;
	var categoriesManager;
	var isResearch = $('#TrouveTout_Research_name').length > 0; //TOSEE


	if (loggedUser || isResearch || isTutorial) {
		caractsManager = new CaractsManager();
		caractsManager.addButtonsForDynamicForms();

		categoriesManager = new CategoriesManager();
		categoriesManager.addButtonsForDynamicForms();
	}
	
	if (!isTutorial) {
		disableSave();
	} else {
		enableSave();
	}
});
