function CategoriesManager() {
	DynamicFormsManager.call(this, 'categories', 'category', 'Ajouter une catégorie parente', 'Supprimer la catégorie'); //TODO traduction
}

$.extend(CategoriesManager.prototype, DynamicFormsManager.prototype);
