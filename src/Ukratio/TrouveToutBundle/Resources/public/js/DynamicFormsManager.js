function DynamicFormsManager(ulName, liName, buttonAddFormValue, buttonDeleteFormValue) {
	var self;

	this.ulName = ulName;
	this.liName = liName;
	this.dynamicForms = $('ul.' + ulName);

	this.addDynamicFormLink = $('<input type="button" class="btn btn-primary" value="' + buttonAddFormValue + '">');

	self = this; // so that the closure are wellformed
    this.addDynamicFormLink.on('click', function(event) {
        self.addDynamicForm();
    });
	this.addDynamicFormLinkLi = $('<li></li>').append(this.addDynamicFormLink);

    this.deleteDynamicFormLink = $('<input type="button" class="btn btn-primary" value="' + buttonDeleteFormValue + '">');

    this.symfonyPrototype = this.dynamicForms.data('prototype');
	this.numberOfForm = this.dynamicForms.find('.' + liName).length;
}


DynamicFormsManager.prototype.addButtonsForDynamicForms = function () {
	var self = this;

    this.dynamicForms.append(this.addDynamicFormLinkLi);
    this.dynamicForms.find('li.' + this.liName).each(function() {
        self.addDeleteDynamicFormLink($(this));
    });		
}


DynamicFormsManager.prototype.addDynamicForm = function () {
	var dynamicForm;
	var dynamicFormLi;

	this.numberOfForm++;
	enableSave();

	dynamicForm = this.symfonyPrototype.replace(/__name__/g, this.numberOfForm - 1);

    dynamicFormLi = $('<li class="' + this.liName + '"></li>').append(dynamicForm);
    dynamicFormLi.find('[id*=selected]').attr('checked', 'checked');
    dynamicFormLi.find('[id*=byDefault]').attr('checked', 'checked');

	dynamicFormLi.find(':input').on('change', function (event) {
		enableSave();
	});

    this.addDeleteDynamicFormLink(dynamicFormLi);

    dynamicFormLi.insertBefore(this.addDynamicFormLinkLi);

	return {'dynamicFormLi':dynamicFormLi, 'index':this.numberOfForm - 1};
}


DynamicFormsManager.prototype.addDeleteDynamicFormLink = function (dynamicFormLi) {
	var deleteDynamicFormLink;
	deleteDynamicFormLink = this.deleteDynamicFormLink.clone();

    deleteDynamicFormLink.on('click', function(event) {
        event.preventDefault();
		enableSave();
        dynamicFormLi.remove();
    });

    dynamicFormLi.append(deleteDynamicFormLink);
}
