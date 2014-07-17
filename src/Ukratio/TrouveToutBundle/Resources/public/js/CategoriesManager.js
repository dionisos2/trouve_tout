function CategoriesManager() {
	DynamicFormsManager.call(this, 'categories', 'category', 'Ajouter une catégorie parente', 'Supprimer la catégorie'); //TODO traduction
}

CategoriesManager.prototype._parentMethods = DynamicFormsManager.prototype;

$.extend(CategoriesManager.prototype, DynamicFormsManager.prototype);

CategoriesManager.prototype._super = function() {
	var methodName = arguments[0];
	var parameters = arguments[1];
	return this._parentMethods[methodName].apply(this, parameters);
}

CategoriesManager.prototype.addButtonsForDynamicForms = function () {
	var self = this;
	this._super('addButtonsForDynamicForms');

	this.dynamicForms.find('li.category').each(function(index) {
        self.addOnChangeEvent($(this), index);
    });

	this.addDynamicFormLink.on('click', function(event) {
        self.updateTemplateAndOwnerCategories(null);
    });
}

CategoriesManager.prototype.addOnChangeEvent = function (categoryForm, index) {
	var self = this;

	categoryForm.find('select').off('change').on('change', function (event) {
		enableSave();
	});

	categoryForm.find('select').off('click').on('click', function (event) {
		self.updateTemplateAndOwnerCategories(categoryForm);
	});
}

CategoriesManager.prototype.addDynamicForm = function () {

	result = this._super('addDynamicForm');
	this.addOnChangeEvent(result.dynamicFormLi, result.index);

	return result;
}

CategoriesManager.prototype.reloadForm = function (categoryForm, index) {
}


CategoriesManager.prototype.updateTemplateAndOwnerCategories = function (categoryForm) {
	var self = this;

	if (!isTutorial) {
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
}

CategoriesManager.prototype.updateTemplateAndOwnerCategoriesCallBack = function (categoriesList) {
	var self = this;

	// delete events
	this.dynamicForms.find('li.' + this.liName).find('select').off('click');

	this.addDynamicFormLink.off()
	this.addDynamicFormLink.on('click', function(event) {
		self.addDynamicForm();
		enableSave();
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
