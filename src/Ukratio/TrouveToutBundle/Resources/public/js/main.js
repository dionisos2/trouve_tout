$(document).ready(function() {
	var caractsManager;
	var categoriesManager;
	var isResearch = $('#TrouveTout_Research_name').length > 0;


	if (loggedUser || isResearch) {
		caractsManager = new CaractsManager();
		caractsManager.addButtonsForDynamicForms();

		categoriesManager = new CategoriesManager();
		categoriesManager.addButtonsForDynamicForms();
	}
});
