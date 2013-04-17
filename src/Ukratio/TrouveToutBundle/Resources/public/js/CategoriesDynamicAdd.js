function addButtonsForCategories()
{
	var categories;
	var addCategoryLink;
	var addCategoryLinkLi;
	
	categories = $('ul.categories');
	addCategoryLink = $('<input type="button" class="btn btn-primary" value="Ajouter une categorie">');
	addCategoryLinkLi = $('<li></li>').append(addCategoryLink);
    categories.append(addCategoryLinkLi);
	
    categories.find('li.category').each(function() {
        addDeleteCategoryLink($(this));
    });		

    addCategoryLink.on('click', function(e) {
        addCategory(categories, addCategoryLinkLi);
    });

}


function addCategory(categories, addCategoryLinkLi) {
	var prototype;
	var index;
	var newForm;
	var newFormL;

    prototype = categories.data('prototype');

    index = categories.find('.category').length;
	
    categoryForm = prototype.replace(/__name__/g, index);

    categoryFormLi = $('<li class="category"></li>').append(categoryForm);
    addDeleteCategoryLink(categoryFormLi);

    categoryFormLi.insertBefore(addCategoryLinkLi);
}


function addDeleteCategoryLink(categoryFormLi) {
	var deleteCategoryLink;

    deleteCategoryLink = $('<a href="#" class="btn btn-primary">Supprimer cette categorie</a>');

    deleteCategoryLink.on('click', function(event) {
        event.preventDefault();
        categoryFormLi.remove();
    });

    categoryFormLi.append(deleteCategoryLink);

}
