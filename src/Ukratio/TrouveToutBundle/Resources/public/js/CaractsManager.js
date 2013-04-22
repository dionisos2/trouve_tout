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
	var parentElements;

	parentElements = this.getParentElement(caractFormLinkLi);

	caractFormLinkLi.find('[id$=value_value]').on('change', function (event)
												  {
													  self.modifyValue($(this), parentElements);
												  }
												 );

	caractFormLinkLi.find('[id$=value_childElement]').on('change', function (event)
														 {
															 self.specifyValue($(this), parentElements);
														 }
														);
}

CaractsManager.prototype.getParentElement = function (caractForm) {
	var parentElements = {};
	var parentBalises = caractForm.find('input[id*=value_element]');
	var parent;

	$.each(parentBalises, 
		   function (index, value) {
			   parentElements[index] = value.attributes['value'].value;
		   }
		  );

	return parentElements;
}

CaractsManager.prototype.getElement = function(caractForm, specified) {
	var data;
	return {ei:"auie",
			uei:"eiu",
			aue:"aue"};

	if (caractForm.get(0).tagName == 'DIV') {
		var test = caractForm.find('[id$=_choice]');
		var hum = caractForm.find('[id$=_choice] option:selected').val();
		if (caractForm.find('[id$=_choice] option:selected').val() == 'other') {
			data = caractForm.find('[id$=_text]').val();
		} else {
			data = caractForm.find('[id$=_choice] option:selected').val();
		}
	} else {
		data = caractForm.val();
	}

	return data;
}

CaractsManager.prototype.modifyValue = function (caractForm, parentElements) {
	var data;
	var self = this;

	data = $.extend(parentElements, this.getElement(caractForm, false));
	
	for (var index in data) {
		alert(index + "->" + data[index]);
	}	

	if (typeof(data) !== 'undefined') {
		$.ajax({
			type: 'POST',
			url: ajaxUrl,
			dataType: 'json',
			data: {elementList: data},

			success: function(data, textStatus, jqXHR) {
				self.updateChildElement(data);
			},

			error: function(jqXHR, textStatus, errorThrown) {
				alert(errorThrown);
			}
		})
	}
}

CaractsManager.prototype.updateChildElement = function (data) {
	var childIndex;
	// for (childIndex in data) {
	// 	alert(childIndex + "->" + data[childIndex]);
	// }
}
