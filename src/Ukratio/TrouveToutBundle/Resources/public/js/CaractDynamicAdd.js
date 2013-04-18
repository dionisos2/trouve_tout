function addButtonsForCaracts()
{
	var caracts;
	var addCaractLink;
	var addCaractLinkLi;

	caracts = $('ul.caracts');
	addCaractLink = $('<input type="button" class="btn btn-primary" value="Ajouter une caractéristique">');
	addCaractLinkLi = $('<li></li>').append(addCaractLink);
    caracts.append(addCaractLinkLi);
	
    caracts.find('li.caract').each(function() {
        addDeleteCaractLink($(this));
    });		

    addCaractLink.on('click', function(e) {
        addCaract(caracts, addCaractLinkLi);
    });
}


function addCaract(caracts, addCaractLinkLi) {
	var prototype;
	var index;
	var newForm;
	var newFormL;

    prototype = caracts.data('prototype');

    index = caracts.find('.caract').length;
	
    caractForm = prototype.replace(/__name__/g, index);

    caractFormLi = $('<li class="caract"></li>').append(caractForm);
    caractFormLi.find('[id*=selected]').attr('checked', 'checked');

    caractFormLi.find('[id*=byDefault]').attr('checked', 'checked');

    addDeleteCaractLink(caractFormLi);

    caractFormLi.insertBefore(addCaractLinkLi);
}


function addDeleteCaractLink(caractFormLi) {
	var deleteCaractLink;

    deleteCaractLink = $('<a href="#" class="btn btn-primary">Supprimer cette caractéristique</a>');

    deleteCaractLink.on('click', function(event) {
        event.preventDefault();
        caractFormLi.remove();
    });

    caractFormLi.append(deleteCaractLink);

}
