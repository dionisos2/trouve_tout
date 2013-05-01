function CaractsManager() {
	DynamicFormsManager.call(this, 'caracts', 'caract', 'Ajouter une caractéristique', 'Supprimer la caractéristique');
	this.prototype_specify = [];

	this.prototype_specify['name'] = $('form').data('prototype_specify_name');
	this.prototype_specify['number'] = $('form').data('prototype_specify_number');
	this.prototype_specify['picture'] = $('form').data('prototype_specify_picture');
	this.prototype_specify['object'] = $('form').data('prototype_specify_object');
	this.prototype_specify['text'] = $('form').data('prototype_specify_text');
}

CaractsManager.prototype._parentMethods = DynamicFormsManager.prototype;
$.extend(CaractsManager.prototype, DynamicFormsManager.prototype);

CaractsManager.prototype._super = function() {
	var methodName = arguments[0];
	var parameters = arguments[1];
	this._parentMethods[methodName].apply(this, parameters);
}

CaractsManager.prototype.addButtonsForDynamicForms = function () {
	var self = this;
	this._super('addButtonsForDynamicForms');

	this.dynamicForms.find('li.' + this.liName).each(function(index) {
        self.addOnChangeEvent($(this), index);
    });		
}

CaractsManager.prototype.addOnChangeEvent = function (caractFormLinkLi, index) {
	var self = this;

	caractFormLinkLi.find('[id$=_type]').on('change', function (event) {
		self.changeValueType(caractFormLinkLi, index);
	}
										   );
		
	caractFormLinkLi.find('[id$=value_value]').on('change', function (event) {
		self.modifyValue(caractFormLinkLi, index);
	}
												 );

	caractFormLinkLi.find('[id$=value_childElement]').on('change', function (event) {
		self.specifyValue(caractFormLinkLi);
	}
														);
}

CaractsManager.prototype.changeValueType = function (caractForm, index) {
	this.changeOrBuildChildForm(caractForm, index);
}

CaractsManager.prototype.getParentElements = function (caractForm) {
	var parentElements = [];
	var parentBalises = caractForm.find('input[id*=value_element]');

	$.each(parentBalises, 
		   function (index, value) {
			   parentElements[index] = value.attributes['value'].value;
		   }
		  );

	parentElements.reverse();
	
	return parentElements;
}

CaractsManager.prototype.getElement = function(caractForm, specified) {
	var element;
	var valueForm;

	valueForm = caractForm.find('[id$=value_value]');

	if (valueForm.get(0).tagName == 'DIV') {
		if (valueForm.find('[id$=_choice] option:selected').val() == 'other') {
			element = valueForm.find('[id$=_text]').val();
		} else {
			element = valueForm.find('[id$=_choice] option:selected').val();
		}
	} else {
		element = valueForm.val();
	}

	return element;
}

CaractsManager.prototype.modifyValue = function (caractForm, index) {
	var completeElement;
	var self = this;
	
	completeElement = this.getParentElements(caractForm);
	completeElement.push(this.getElement(caractForm, false));

	if (typeof(completeElement) !== 'undefined') {
		$.ajax({
			type: 'POST',
			url: ajaxUrl,
			dataType: 'json',
			data: {completeElement: completeElement,
				   type: caractForm.find('[id$=type]').val()},

			success: function(elementsList, textStatus, jqXHR) {
				self.updateChildElement(caractForm, elementsList, index);
			},

			error: function(jqXHR, textStatus, errorThrown) {
				alert(errorThrown);
			}
		})
	}
}

CaractsManager.prototype.getChildForm = function (caractForm) {
	
	if (caractForm.find('[id$=value_childElement_choice]').length > 0) {
		return caractForm.find('[id$=value_childElement_choice]');
	} else {
		return caractForm.find('[id$=value_childElement]');
	}
}

CaractsManager.prototype.getType = function (caractForm) {
	return caractForm.find('[id$=_type]').val();
}

CaractsManager.prototype.buildChildForm = function(caractForm, index) {
	var type;
	var childFormSelectDiv;

	type = this.getType(caractForm);
	childFormSelectDiv = this.prototype_specify[type].replace(/childElement/g, 'caracts_' + (index).toString() + '_value_childElement');
	childFormSelectDiv = childFormSelectDiv.replace(/(name=.*?)\[.*?\]/g, '$1[caracts][' + (index).toString() + '][value][childElement]');
	caractForm.find('[id$=value_value]').parent().parent().parent().find('div:first').prepend(childFormSelectDiv);
}

CaractsManager.prototype.removeChildForm = function(caractForm) {
	caractForm.find('[id$=value_childElement]').prev().remove(); //remove label
	caractForm.find('[id$=value_childElement]').remove();
}

CaractsManager.prototype.changeOrBuildChildForm = function (caractForm, index) {
	this.removeChildForm(caractForm);
	this.buildChildForm(caractForm, index);
}

CaractsManager.prototype.getOrBuildChildForm = function (caractForm, index) {
	var childFormSelect;

	childFormSelect = this.getChildForm(caractForm);

	if (childFormSelect.length > 0) {
		return childFormSelect;
	} else {
		this.buildChildForm(caractForm, index);
		childFormSelect = this.getChildForm(caractForm);
		return childFormSelect;
	}
}

CaractsManager.prototype.updateChildElement = function (caractForm, elementsList, index) {
	var childIndex;
	var childFormSelect;
	var options;

	
	childFormSelect = this.getOrBuildChildForm(caractForm, index);

	$('option', childFormSelect).remove();

	if(childFormSelect.prop) {
	  options = childFormSelect.prop('options');
	} else {
	  options = childFormSelect.attr('options');
	}

	$.each(elementsList, function(value, text) {
		options[options.length] = new Option(text, value);
	});
	
}

