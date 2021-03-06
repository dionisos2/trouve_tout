function CaractsManager() {
	DynamicFormsManager.call(this, 'caracts', 'caract', 'Ajouter une caractéristique', 'Supprimer la caractéristique');

	// childValue is for the specification form, and value is for create the form to change and show the current value
	this.prototypeOf = [];
	this.prototypeOf['childValue'] = [];
	this.prototypeOf['value'] = [];

	this.haveChildValue = []
	this.haveChildValue['name'] = true;
	this.haveChildValue['number'] = false;
	this.haveChildValue['picture'] = true;
	this.haveChildValue['object'] = true;
	this.haveChildValue['text'] = false;
	this.haveChildValue['date'] = false;

	//TODO with foreach
	this.prototypeOf['childValue']['name'] = $('form').data('prototype of child value name');
	this.prototypeOf['childValue']['number'] = null;
	this.prototypeOf['childValue']['picture'] = $('form').data('prototype of child value picture');
	this.prototypeOf['childValue']['object'] = $('form').data('prototype of child value object');
	this.prototypeOf['childValue']['text'] = null;
	this.prototypeOf['childValue']['date'] = null;

	this.prototypeOf['value']['name'] = $('form').data('prototype of value name');
	this.prototypeOf['value']['number'] = $('form').data('prototype of value number');
	this.prototypeOf['value']['picture'] = $('form').data('prototype of value picture');
	this.prototypeOf['value']['object'] = $('form').data('prototype of value object');
	this.prototypeOf['value']['text'] = $('form').data('prototype of value text');
	this.prototypeOf['value']['date'] = $('form').data('prototype of value date');

	this.prototypeOf['ownerElement'] = $('form').data('prototype of owner element');
	this.prototypeOf['imprecision'] = $('form').data('prototype of imprecision');
	this.prototypeOf['prefix'] = $('form').data('prototype of prefix');
	this.prototypeOf['unit'] = $('form').data('prototype of unit');
	this.prototypeOf['choosePicture'] = $('form').data('prototype of choose_picture');
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

	this.dynamicForms.find('li.caract').each(function(index) {
        self.addOnChangeEvent($(this), index);
		self.addOrDeleteChoosePictureButton($(this), index);
    });
}

CaractsManager.prototype.addOrDeleteChoosePictureButton = function(caractForm, index) {
	var self = this;
	var choosePictureDiv;

	choosePictureDiv = caractForm.find('[id=choosePictureDiv]');
	choosePictureDiv.empty();

	if (this.getType(caractForm) == 'picture') {
		choosePictureButton = this.namePrototype(caractForm, index, this.prototypeOf['choosePicture'], ['choosePicture']);

		choosePictureDiv.append(choosePictureButton);
	}
}

CaractsManager.prototype.uploadPicture = function(caractForm, index) {
	alert("plop");
}

CaractsManager.prototype.reloadPicture = function (caractForm) {
	var pictureForm, pictureSrc;

	pictureForm = caractForm.find('[name=caract_picture]');

	pictureSrc = this.getParentElements(caractForm);
	pictureSrc.unshift(this.getValue(caractForm, false));
	pictureSrc.reverse();
	pictureSrc = pictureSrc.join("/");
	pictureForm.attr("src", pictureUrl + pictureSrc);
	pictureForm.attr("alt", pictureSrc);
	pictureForm.parent().attr("href", pictureUrl + pictureSrc);
}

CaractsManager.prototype.reloadForm = function (caractForm, index) {
	var value, childValue;

	type = this.getType(caractForm);
	value = this.getValue(caractForm, false);
	childValue = this.getValue(caractForm, true);

	if (this.needUpdate(type)) {
		this.updateValueForm(caractForm, index, false);
		this.setValue(caractForm, value, false);

		this.updateValueForm(caractForm, index, true);
		this.setValue(caractForm, childValue, true);
	}
}


CaractsManager.prototype.addOnChangeEvent = function (caractForm, index) {
	var self = this;
	var valueForm, childValueForm;

	caractForm.find(':input:not(:button)').off().on('change', function (event) {
		enableSave();
	});

	caractForm.find('[id$=_type]').on('change', function (event) {
		self.changeValueType(caractForm, index);
	});

	valueForm = this.getValueForm(caractForm, false);
	valueForm.on('change', function (event) {
		self.modifyValue(caractForm, index);
	});

	childValueForm = this.getValueForm(caractForm, true);
	childValueForm.on('change', function (event) {
		self.specifyValue(caractForm, index);
	});

	caractForm.find('[id*=_value_element_]').on('dblclick keypress', function (event) {
		self.generalize(caractForm, index, this);
	});

}

CaractsManager.prototype.needUpdate = function (type) {
	return ((type == 'name') || (type == 'object') || (type == 'picture'));
}

CaractsManager.prototype.generalize = function (caractForm, index, ownerElementForm) {
	var value, type;
	type = this.getType(caractForm);

	if (this.needUpdate(type)) {
		value = ownerElementForm.value;
		$(ownerElementForm).parent().prevAll().remove();
		$(ownerElementForm).parent().remove();
		this.updateValueForm(caractForm, index, false);
		this.setValue(caractForm, value, false);
		this.updateValueForm(caractForm, index, true);
		enableSave();

		if (this.getType(caractForm) == "picture") {
			this.reloadPicture(caractForm);
		}
	}
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

	if (this.getType(caractForm) == "picture") {
		this.reloadPicture(caractForm);
	}
}

CaractsManager.prototype.addOwnerElement = function (caractForm, index, value) {
	var ownerElementForm;
	var ownerIndex;
	var self = this;

	ownerIndex = this.getParentElements(caractForm).length.toString();

	//TODO use namePrototype
	ownerElementForm = this.prototypeOf['ownerElement'].replace(/__name__/g, 'caracts_' + (index).toString() + '_value_element_' + ownerIndex);
	ownerElementForm = ownerElementForm.replace(/(name=.*?)\[.*?\]/g, '$1[caracts][' + (index).toString() + '][value][element_' + ownerIndex + ']');
	ownerElementForm = $(ownerElementForm); // /!\ warning here
	ownerElementForm.find('input').val(value);
	ownerElementForm.find('input').on('dblclick keypress', function (event) {
		self.generalize(caractForm, index, this);
	});
	caractForm.find('#ownerElementDiv').prepend(ownerElementForm);
}

CaractsManager.prototype.addDynamicForm = function () {

	result = this._super('addDynamicForm');
	this.addOnChangeEvent(result.dynamicFormLi, result.index);
	this.updateValueForm(result.dynamicFormLi, result.index, false);
	this.addOrDeleteChoosePictureButton(result.dynamicFormLi, result.index);

	return result;
}

CaractsManager.prototype.removeOwnerElements = function (caractForm, index) {
	caractForm.find('#ownerElementDiv').empty();
}

CaractsManager.prototype.changeOwnerElements = function (caractForm, index) {
	var type;
	type = this.getType(caractForm);

	if (!this.haveChildValue[type]) {
		this.removeOwnerElements(caractForm, index);
		this.addOwnerElement(caractForm, index, type);
	}
}

CaractsManager.prototype.namePrototype = function (caractForm, index, prototype_, names) {
	var endOfName;
	var i;

	endOfName = '';
	for (i in names) {
		endOfName += '_' + names[i] ;
	}
	prototype_ = prototype_.replace(/__name__/g, 'caracts_' + (index).toString() + endOfName);

	endOfName = '';
	for (i in names) {
		endOfName += '[' + names[i] + ']';
	}

	prototype_ = prototype_.replace(/(name=.*?)\[.*?\]/g, '$1[caracts][' + (index).toString() + ']' + endOfName);

	return prototype_;
}

CaractsManager.prototype.buildNumberForms = function (caractForm, index) {
	var imprecisionForm, prefixForm, unitForm, valueDiv;

	imprecisionForm = caractForm.find('[id$=imprecision]');
	if (imprecisionForm.length == 0) {
		valueDiv = caractForm.find('#valueDiv');

		imprecisionForm = this.namePrototype(caractForm, index, this.prototypeOf['imprecision'], ['imprecision']);
		prefixForm = this.namePrototype(caractForm, index, this.prototypeOf['prefix'], ['prefix']);
		unitForm = this.namePrototype(caractForm, index, this.prototypeOf['unit'], ['unit']);

		valueDiv.append(imprecisionForm);
		valueDiv.append(prefixForm);
		valueDiv.append(unitForm);
	}
}

CaractsManager.prototype.updateNumberForms = function (caractForm, index) {
	caractForm.find('[id$=imprecision]').val(0);
}

CaractsManager.prototype.deleteNumberForms = function (caractForm, index) {
	var imprecisionForm;
	imprecisionForm = caractForm.find('[id$=imprecision]');

	if (imprecisionForm.length == 1) {
		imprecisionForm.parent().remove();
		caractForm.find('[id$=prefix]').parent().remove();
		caractForm.find('[id$=unit]').parent().remove();
	}
}

CaractsManager.prototype.replaceAllNumberForms = function () {
	var self = this;
	this.dynamicForms.find('li.' + this.liName).each(function(index) {
		if (self.getType($(this)) == 'number') {
			self.replaceNumberForms($(this), index);
		}
    });
}

CaractsManager.prototype.replaceNumberForms = function (caractForm, index) {
	var imprecisionValue, prefixValue, unitValue;
	var imprecisionForm, prefixForm, unitForm;

	imprecisionForm = caractForm.find('[id$=imprecision]');
	prefixForm = caractForm.find('[id$=prefix]');
	unitForm = caractForm.find('[id$=unit]');

	imprecisionValue = imprecisionForm.val();
	prefixValue = prefixForm.val();
	unitValue = unitForm.val();

	this.deleteNumberForms(caractForm, index);
	this.buildNumberForms(caractForm, index);

	imprecisionForm = caractForm.find('[id$=imprecision]');
	prefixForm = caractForm.find('[id$=prefix]');
	unitForm = caractForm.find('[id$=unit]');

	imprecisionForm.val(imprecisionValue);
	prefixForm.val(prefixValue);
	unitForm.val(unitValue);
}

CaractsManager.prototype.changeValueType = function (caractForm, index) {
	var value;
	var childValue;
	var type;

	type = this.getType(caractForm);
	value = this.getValue(caractForm, false);
	childValue = this.getValue(caractForm, true);

	this.changeOwnerElements(caractForm, index);

	this.changeOrBuildValueForm(caractForm, index, false);
	this.changeOrBuildValueForm(caractForm, index, true);

	if (this.getType(caractForm) == 'number') {
		this.buildNumberForms(caractForm, index);
		this.updateNumberForms(caractForm, index);
	} else {
		this.deleteNumberForms(caractForm, index);
	}

	this.updateValueForm(caractForm, index, false);
	this.setValue(caractForm, value, false);

	this.updateValueForm(caractForm, index, true);
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

CaractsManager.prototype.setValue = function(caractForm, value, isChildElement) {
	var valueForm;
	var options;
	var selectForm;

	valueForm = this.getValueForm(caractForm, isChildElement);


	if(valueForm.length == 0) {
		if(isChildElement) {
			return null;
		} else {
			throw "caractForm don’t have value or childValue 'property'";
		}
	}

	if (valueForm.get(0).tagName == 'DIV') { //compound element
		selectForm = valueForm.find('[id$=_choice]');
		if (selectForm.find('option[value="' + value + '"]').length > 0) {
			selectForm.val(translate(value, language));
			valueForm.find('[id$=_text]').val('');
		} else {
			selectForm.val(translate('other', language));
			valueForm.find('[id$=_text]').val(translate(value, language));
		}
	} else {
		valueForm.val(value);
	}
}

CaractsManager.prototype.getValue = function(caractForm, isChildElement) {
	var element;
	var valueForm;

	valueForm = this.getValueForm(caractForm, isChildElement);

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

CaractsManager.prototype.getValueForm = function (caractForm, isChildElement) {
	if (isChildElement) {
		if (caractForm.find('[id$=value_childValue_choice]').length > 0) {
			return caractForm.find('[id$=value_childValue_choice]').parent();
		} else {
			return caractForm.find('[id$=value_childValue]');
		}
	} else {
		if (caractForm.find('[id$=value_value_choice]').length > 0) {
			return caractForm.find('[id$=value_value_choice]').parent();
		} else {
			return caractForm.find('[id$=value_value]');
		}
	}
}

CaractsManager.prototype.getFirstValueForm = function (caractForm, isChildElement) {
	var valueForm;

	valueForm = this.getValueForm(caractForm, isChildElement);

	if (valueForm.length == 0) {
		return valueForm;
	}

	if (valueForm.get(0).tagName == 'DIV') {
		return valueForm.find('[id$=choice]');
	} else {
		return valueForm;
	}
}

CaractsManager.prototype.getSecondValueForm = function (caractForm, isChildElement) {
	var valueForm;

	valueForm = this.getValueForm(caractForm, isChildElement);

	if (valueForm.length == 0) {
		return valueForm;
	}

	if (valueForm.get(0).tagName == 'DIV') {
		return valueForm.find('[id$=text]');
	} else {
		return valueForm;
	}
}

CaractsManager.prototype.modifyValue = function (caractForm, index) {
	var type = this.getType(caractForm);

	this.updateValueForm(caractForm, index, true);

	if (type == 'picture') {
		this.reloadPicture(caractForm);
	}
}

CaractsManager.prototype.updateValueForm = function (caractForm, index, isChildElement) {
	var completeElement;
	var self = this;

	if(!this.needUpdate(this.getType(caractForm))) {
		return 0;
	}

	completeElement = this.getParentElements(caractForm);

	if (isChildElement) {
		if (this.getValue(caractForm, false) !== null) {
			completeElement.unshift(this.getValue(caractForm, false));
		} else {
			self.updateValueFormCallBack(caractForm, [['other', translate('other', language)]], index, isChildElement);
			return 0;
		}
	}

	if (completeElement.length == 0) {
		if (isChildElement) {
			self.updateValueFormCallBack(caractForm, [['other', translate('other', language)]], index, isChildElement);
			return 0;
		} else {
			completeElement = 'empty'; //strange problem with .ajax
		}
	}

	if (typeof(completeElement) !== 'undefined') {
		$('#ajax-loading').show();
		$('#ajax-loading').prependTo(caractForm);
		$.ajax({
			async: false,
			type: 'POST',
			url: ajaxModifyCaractUrl,
			dataType: 'json',
			data: {'completeElement': completeElement,
				   'type': this.getType(caractForm),
				   'isChildElement': isChildElement
				  },

			success: function(elementsList, textStatus, jqXHR) {
				self.updateValueFormCallBack(caractForm, elementsList, index, isChildElement);
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

CaractsManager.prototype.getType = function (caractForm) {
	return caractForm.find('[id$=_type]').val();
}

CaractsManager.prototype.buildValueForm = function(caractForm, index, isChildElement) {
	var type;
	var childFormSelectDiv;
	var FormSelectDiv;
	var caractObjectLink;
	var self = this;

	type = this.getType(caractForm);

	//TODO use namePrototype
	if (isChildElement) {
		if (this.prototypeOf['childValue'][type] != null) {
			FormSelectDiv = this.prototypeOf['childValue'][type].replace(/__name__/g, 'caracts_' + (index).toString() + '_value_childValue');
			FormSelectDiv = FormSelectDiv.replace(/(name=.*?)\[.*?\]/g, '$1[caracts][' + (index).toString() + '][value][childValue]');
			caractForm.find('#childValueDiv').prepend(FormSelectDiv);
		}
	} else {
		FormSelectDiv = this.prototypeOf['value'][type].replace(/__name__/g, 'caracts_' + (index).toString() + '_value_value');
		FormSelectDiv = FormSelectDiv.replace(/(name=.*?)\[.*?\]/g, '$1[caracts][' + (index).toString() + '][value][value]');
		caractForm.find('#valueDiv').prepend(FormSelectDiv);
	}

	if (type != 'picture') {
		caractForm.find('[name=caract_picture]').attr('src', '');
		caractForm.find('[name=caract_picture]').attr('alt', '');
	}

	if (type != 'object') {
		caractObjectLink = caractForm.find('[name=caract_object]');
		if (caractObjectLink.length > 0) {
			caractObjectLink.remove();
		}
	}
	this.addOnChangeEvent(caractForm, index);
	this.addOrDeleteChoosePictureButton(caractForm, index);
}

CaractsManager.prototype.removeValueForm = function(caractForm, isChildElement) {
	var childForm;

	if (isChildElement) {
		caractForm.find('[id=childValueDiv]').empty();
	} else {
		caractForm.find('[id=valueDiv]').empty();
	}
}

CaractsManager.prototype.changeOrBuildValueForm = function (caractForm, index, isChildElement) {
	this.removeValueForm(caractForm, isChildElement);
	this.buildValueForm(caractForm, index, isChildElement);
}

CaractsManager.prototype.getFirstOrBuildValueForm = function (caractForm, index, isChildElement) {
	var formSelect;

	formSelect = this.getFirstValueForm(caractForm, isChildElement);

	if (formSelect.length > 0) {
		return formSelect;
	} else {
		this.buildValueForm(caractForm, index, isChildElement);
		formSelect = this.getFirstValueForm(caractForm, isChildElement);
		return formSelect;
	}
}

CaractsManager.prototype.updateValueFormCallBack = function (caractForm, elementsList, index, isChildElement) {
	var formSelect;
	var tmpElementList

	formSelect = this.getFirstOrBuildValueForm(caractForm, index, isChildElement);

	$('option', formSelect).remove();

	if (formSelect.is('select')) {
		$.each(elementsList, function(key, value) {
			formSelect.append(new Option(translate(value[0], language), value[1]));
		});
	}

}

