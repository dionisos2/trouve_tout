function CaractsManager() {
	DynamicFormsManager.call(this, 'caracts', 'caract', 'Ajouter une caractéristique', 'Supprimer la caractéristique');
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

	this.dynamicForms.find('li.' + this.liName).each(function() {
        self.addOnChangeEvent($(this));
    });		
}

CaractsManager.prototype.addOnChangeEvent = function (caractFormLinkLi) {
	var self = this;

	caractFormLinkLi.find('[id$=value_value]').on('change', function (event)
												  {
													  self.modifyValue(caractFormLinkLi);
												  }
												 );

	caractFormLinkLi.find('[id$=value_childElement]').on('change', function (event)
														 {
															 self.specifyValue(caractFormLinkLi);
														 }
														);
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

CaractsManager.prototype.modifyValue = function (caractForm) {
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
				self.updateChildElement(caractForm, elementsList);
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

CaractsManager.prototype.updateChildElement = function (caractForm, elementsList) {
	var childIndex;
	var childFormSelect;
	var options;

	
	childFormSelect = this.getChildForm(caractForm);

	$('option', childFormSelect).remove();

	if(childFormSelect.prop) {
	  options = childFormSelect.prop('options');
	}
	else {
	  options = childFormSelect.attr('options');
	}

	$.each(elementsList, function(value, text) {
		options[options.length] = new Option(text, value);
	});
	
	// for (childIndex in elementsList) {
	// 	alert(childIndex + "->" + elementsList[childIndex]);
	// }
}
