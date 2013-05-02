function CaractsManager() {
	DynamicFormsManager.call(this, 'caracts', 'caract', 'Ajouter une caractéristique', 'Supprimer la caractéristique');
	this.prototypeOf = [];
	this.prototypeOf['childValue'] = [];
	this.prototypeOf['value'] = [];

	this.prototypeOf['childValue']['name'] = $('form').data('prototype of child value name');
	this.prototypeOf['childValue']['number'] = $('form').data('prototype of child value number');
	this.prototypeOf['childValue']['picture'] = $('form').data('prototype of child value picture');
	this.prototypeOf['childValue']['object'] = $('form').data('prototype of child value object');
	this.prototypeOf['childValue']['text'] = $('form').data('prototype of child value text');

	this.prototypeOf['value']['name'] = $('form').data('prototype of value name');
	this.prototypeOf['value']['number'] = $('form').data('prototype of value number');
	this.prototypeOf['value']['picture'] = $('form').data('prototype of value picture');
	this.prototypeOf['value']['object'] = $('form').data('prototype of value object');
	this.prototypeOf['value']['text'] = $('form').data('prototype of value text');

	this.prototypeOf['ownerElement'] = $('form').data('prototype of owner element');
}

CaractsManager.prototype._parentMethods = DynamicFormsManager.prototype;
$.extend(CaractsManager.prototype, DynamicFormsManager.prototype);

CaractsManager.prototype._super = function() {
	var methodName = arguments[0];
	var parameters = arguments[1];
	return this._parentMethods[methodName].apply(this, parameters);
}

CaractsManager.prototype.addButtonsForDynamicForms = function () {
	var self = this;
	this._super('addButtonsForDynamicForms');

	this.dynamicForms.find('li.' + this.liName).each(function(index) {
        self.addOnChangeEvent($(this), index);
    });
}

CaractsManager.prototype.addOwnerElementOnChangeEvent = function (caractForm, index) {
	
}

CaractsManager.prototype.addOnChangeEvent = function (caractForm, index) {
	var self = this;

	caractForm.find('[id$=_type]').off();
	caractForm.find('[id$=value_value]').off();
	caractForm.find('[id$=value_childValue]').off();
	caractForm.find('[id*=_value_element_]').off();

	caractForm.find('[id$=_type]').on('change', function (event) {
		self.changeValueType(caractForm, index);
	});
		
	caractForm.find('[id$=value_value]').on('change', function (event) {
		self.modifyValue(caractForm, index);
	});

	caractForm.find('[id$=value_childValue]').on('change', function (event) {
		self.specifyValue(caractForm, index);
	});

	caractForm.find('[id*=_value_element_]').on('dblclick', function (event) {
		self.generalize(caractForm, index, this);
	});

}

CaractsManager.prototype.generalize = function (caractForm, index, ownerElementForm) {
	var value;

	value = ownerElementForm.value;
	$(ownerElementForm).parent().prevAll().remove();
	$(ownerElementForm).parent().remove();
	this.updateValueForm(caractForm, index, false);
	this.setValue(caractForm, value, false);
	this.updateValueForm(caractForm, index, true);
}

CaractsManager.prototype.specifyValue = function (caractForm, index) {
	var value;
	var childValue;

	value = this.getValue(caractForm, false);
	childValue = this.getValue(caractForm, true);

	if ((value)&&(childValue)) {
		this.addOwnerElement(caractForm, index, value);
		this.updateValueForm(caractForm, index, false);
		this.setValue(caractForm, childValue, false);
		this.updateValueForm(caractForm, index, true);
		this.setValue(caractForm, "", true);
	}
}

CaractsManager.prototype.addOwnerElement = function (caractForm, index, value) {
	var ownerElementForm;
	var ownerIndex;
	var self = this;

	ownerIndex = this.getParentElements(caractForm).length.toString();

	ownerElementForm = this.prototypeOf['ownerElement'].replace(/__name__/g, 'caracts_' + (index).toString() + '_value_element_' + ownerIndex);
	ownerElementForm = ownerElementForm.replace(/(name=.*?)\[.*?\]/g, '$1[caracts][' + (index).toString() + '][value][value][element_' + ownerIndex + ']');
	ownerElementForm = $(ownerElementForm); // /!\ warning here
	ownerElementForm.find('input').val(value);
	ownerElementForm.find('input').on('dblclick', function (event) {
		self.generalize(caractForm, index, this);
	});
	caractForm.find('#restDiv').prepend(ownerElementForm);
}

CaractsManager.prototype.addDynamicForm = function () {

	result = this._super('addDynamicForm');
	this.addOnChangeEvent(result.dynamicFormLi, result.index);
	this.updateValueForm(result.dynamicFormLi, result.index, false);
}

CaractsManager.prototype.changeValueType = function (caractForm, index) {
	var value;
	var childValue;

	value = this.getValue(caractForm, false);
	childValue = this.getValue(caractForm, true);

	this.changeOrBuildValueForm(caractForm, index, false);
	this.changeOrBuildValueForm(caractForm, index, true);
	this.updateValueForm(caractForm, index, false);
	this.updateValueForm(caractForm, index, true);

	this.setValue(caractForm, value, false);
	this.setValue(caractForm, childValue, true);
}

CaractsManager.prototype.getParentElements = function (caractForm) {
	var parentElements = [];
	var parentBalises = caractForm.find('input[id*=value_element]');

	$.each(parentBalises, 
		   function (index, value) {
			   parentElements[index] = value.value;
		   }
		  );
	
	return parentElements;
}
CaractsManager.prototype.setValue = function(caractForm, value, childValue) {
	var valueForm;
	var options;
	var selectForm;

	if(childValue) {
		valueForm = caractForm.find('[id$=value_childValue]');
	} else {
		valueForm = caractForm.find('[id$=value_value]');
	}


	if(valueForm.length == 0) {
		throw "caractForm don’t have value or childValue 'property'";
	}

	if (valueForm.get(0).tagName == 'DIV') { //compound element
		selectForm = valueForm.find('[id$=_choice]');
		if (selectForm.find('option[value="' + value + '"]').length > 0) {
			selectForm.val(value);
			valueForm.find('[id$=_text]').val('');
		} else {
			selectForm.val('other');
			valueForm.find('[id$=_text]').val(value);
		}
	} else {
		valueForm.val(value);
	}
	
}

CaractsManager.prototype.getValue = function(caractForm, childValue) {
	var element;
	var valueForm;

	if(childValue) {
		valueForm = caractForm.find('[id$=value_childValue]');
	} else {
		valueForm = caractForm.find('[id$=value_value]');
	}


	if(valueForm.length == 0) {
		return null;
	}

	if (valueForm.get(0).tagName == 'DIV') { //compound element
		if (valueForm.find('[id$=_choice] option:selected').val() == 'other') {
			value = valueForm.find('[id$=_text]').val();
		} else {
			value = valueForm.find('[id$=_choice] option:selected').val();
		}
	} else {
		value = valueForm.val();
	}

	return value;
}

CaractsManager.prototype.getValueFirstForm = function (caractForm, childValue) {
	
	if (childValue) {
		if (caractForm.find('[id$=value_childValue_choice]').length > 0) {
			return caractForm.find('[id$=value_childValue_choice]');
		} else {
			return caractForm.find('[id$=value_childValue]');
		}
	} else {
		if (caractForm.find('[id$=value_value_choice]').length > 0) {
			return caractForm.find('[id$=value_value_choice]');
		} else {
			return caractForm.find('[id$=value_value]');
		}
	}
}

CaractsManager.prototype.modifyValue = function (caractForm, index) {
	this.updateValueForm(caractForm, index, true);
}

CaractsManager.prototype.updateValueForm = function (caractForm, index, childValue) {
	var completeElement;
	var self = this;
	
	completeElement = this.getParentElements(caractForm);

	if (childValue) {
		if (this.getValue(caractForm, false)) {
			completeElement.unshift(this.getValue(caractForm, false));
		} else {
			self.updateValueFormCallBack(caractForm, {'other':'other'}, index, childValue);
			return 0;
		}
	}

	if (completeElement.length == 0) {
		if (childValue) {
			self.updateValueFormCallBack(caractForm, {'other':'other'}, index, childValue);
			return 0;
		} else {
			completeElement = 'empty'; //strange problem with .ajax
		}
	}

	if (typeof(completeElement) !== 'undefined') {
		$.ajax({
			async: false,
			type: 'POST',
			url: ajaxUrl,
			dataType: 'json',
			data: {'completeElement': completeElement,
				   'type': this.getType(caractForm)
				  },

			success: function(elementsList, textStatus, jqXHR) {
				self.updateValueFormCallBack(caractForm, elementsList, index, childValue);
			},

			error: function(jqXHR, textStatus, errorThrown) {
				alert(errorThrown);
			}
		})
	}
}

CaractsManager.prototype.getType = function (caractForm) {
	return caractForm.find('[id$=_type]').val();
}

CaractsManager.prototype.buildValueForm = function(caractForm, index, childValue) {
	var type;
	var childFormSelectDiv;
	var FormSelectDiv;
	var self = this;

	type = this.getType(caractForm);

	if (childValue) {
		FormSelectDiv = this.prototypeOf['childValue'][type].replace(/__name__/g, 'caracts_' + (index).toString() + '_value_childValue');
		FormSelectDiv = FormSelectDiv.replace(/(name=.*?)\[.*?\]/g, '$1[caracts][' + (index).toString() + '][value][childValue]');
		caractForm.find('#childValueDiv').prepend(FormSelectDiv);
	} else {
		FormSelectDiv = this.prototypeOf['value'][type].replace(/__name__/g, 'caracts_' + (index).toString() + '_value_value');
		FormSelectDiv = FormSelectDiv.replace(/(name=.*?)\[.*?\]/g, '$1[caracts][' + (index).toString() + '][value][value]');
		caractForm.find('#valueDiv').prepend(FormSelectDiv);
	}

	this.addOnChangeEvent(caractForm, index);
}

CaractsManager.prototype.removeValueForm = function(caractForm, childValue) {

	if (childValue) {
		caractForm.find('[id$=value_childValue]').parent().remove();
	} else {
		caractForm.find('[id$=value_value]').parent().remove();
	}
}

CaractsManager.prototype.changeOrBuildValueForm = function (caractForm, index, childValue) {
	this.removeValueForm(caractForm, childValue);
	this.buildValueForm(caractForm, index, childValue);
}

CaractsManager.prototype.getFirstOrBuildValueForm = function (caractForm, index, childValue) {
	var formSelect;

	formSelect = this.getValueFirstForm(caractForm, childValue);

	if (formSelect.length > 0) {
		return formSelect;
	} else {
		this.buildValueForm(caractForm, index, childValue);
		formSelect = this.getValueFirstForm(caractForm, childValue);
		return formSelect;
	}
}

CaractsManager.prototype.updateValueFormCallBack = function (caractForm, elementsList, index, childValue) {
	var formSelect;
	
	formSelect = this.getFirstOrBuildValueForm(caractForm, index, childValue);

	$('option', formSelect).remove();

	if (formSelect.is('select')) {
		$.each(elementsList, function(value, text) {
			formSelect.append(new Option(text, value));
		});
	}
	
}

