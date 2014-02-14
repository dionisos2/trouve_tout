function CategoriesManager() {
	DynamicFormsManager.call(this, 'categories', 'category', 'Ajouter une catégorie parente', 'Supprimer la catégorie'); //TODO traduction
	this.addOwnerCategoriesEvent();
}

$.extend(CategoriesManager.prototype, DynamicFormsManager.prototype);

CategoriesManager.prototype.addOwnerCategoriesEvent = function() {
	var self = this;

	this.dynamicForms.find('li.' + this.liName).each(function(index) {
        self.addEvents($(this), index);
	});

	this.addDynamicFormLink.on('click', function(event) {
        self.updateTemplateAndOwnerCategories(null);
    });
}

CategoriesManager.prototype.addEvents = function (categoryForm, index) {
	var self = this;

	categoryForm.find('select').on('click', function (event) {
		self.updateTemplateAndOwnerCategories(categoryForm);
	});
}

CategoriesManager.prototype.updateTemplateAndOwnerCategories = function (categoryForm) {
	var self = this;

	$('#ajax-loading').show();
	if (categoryForm != null) {
		$('#ajax-loading').prependTo(categoryForm);
	} else {
		$('#ajax-loading').appendTo($('#ul_categories'));
	}
	$.ajax({
		async: false,
		type: 'POST',
		url: ajaxGetOwnerCategoryUrl,
		dataType: 'json',
		data: {'conceptId': (conceptId == null)? 'empty':conceptId,
			  },

		success: function(categoriesList, textStatus, jqXHR) {
			self.updateTemplateAndOwnerCategoriesCallBack(categoriesList);
			$('#ajax-loading').hide();
			$('#ajax-loading').prependTo($('body'));
		},

		error: function(jqXHR, textStatus, errorThrown) {
			alert(errorThrown);
			$('#ajax-loading').hide();
			$('#ajax-loading').prependTo($('body'));
		}
	})
}

CategoriesManager.prototype.updateTemplateAndOwnerCategoriesCallBack = function (categoriesList) {
	var self = this;

	// delete events
	this.dynamicForms.find('li.' + this.liName).find('select').off();

	this.addDynamicFormLink.off()
	this.addDynamicFormLink.on('click', function(event) {
		self.addDynamicForm();
    });

	this.changeOwnerCategoryForms(categoriesList);
	this.changeOwnerCategoryTemplate(categoriesList);
}

CategoriesManager.prototype.changeOwnerCategoryForms = function (categoriesList) {
	var self = this;

	this.dynamicForms.find('li.' + this.liName).each(function(index) {
        self.changeOwnerCategoryForm($(this), index, categoriesList);
	});
}

CategoriesManager.prototype.changeOwnerCategoryForm = function (categoryForm, index, categoriesList) {
	var formSelect;
	var value;

	formSelect = categoryForm.find('select');
	value = formSelect.find('option:selected').val();

	$('option', formSelect).remove();

	$.each(categoriesList, function(key, value) {
		formSelect.append(new Option(value, key));
	});

	formSelect.val(value); //TODO less risky thing when the category is missing
}

CategoriesManager.prototype.changeOwnerCategoryTemplate = function (categoriesList) {
	var strOptions = '$1';

	$.each(categoriesList, function(key, value) {
		strOptions += '<option value="' + key + '">' + value + '</option>';
	});

	strOptions += '$2';
	this.symfonyPrototype = this.symfonyPrototype.replace(/(<select.*?>).*(<\/select>)/g, strOptions);
}
