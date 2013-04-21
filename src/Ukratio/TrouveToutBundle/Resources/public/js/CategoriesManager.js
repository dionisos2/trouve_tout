function CategoriesManager() {
	DynamicFormsManager.call(this, 'categories', 'category', 'Ajouter une catégorie', 'Supprimer la catégorie');
}

$.extend(CategoriesManager.prototype, DynamicFormsManager.prototype);

