$(document).ready(function() {
	var caractsManager;
	var categoriesManager;

	caractsManager = new CaractsManager();
	caractsManager.addButtonsForDynamicForms();

	categoriesManager = new CategoriesManager();
	categoriesManager.addButtonsForDynamicForms();
});
