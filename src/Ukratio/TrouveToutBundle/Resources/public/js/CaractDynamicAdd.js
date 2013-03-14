var collectionHolderConcept = $('ul.concepts');

var $addConceptLink = $('<li><a href="#" class="btn btn-primary">Add a concept</a></li>');

var collectionHolderCaract = $('ul.caracts');

var $addCaractLink = $('<li><a href="#" class="btn btn-primary">Add a caract</a></li>');

jQuery(document).ready(function() {

    collectionHolderCaract.find('li.caract').each(function() {
        addCaractFormDeleteLink($(this));
    });		
	
    collectionHolderCaract.append($addCaractLink);

    collectionHolderCaract.data('index', collectionHolderCaract.find(':input').length);

    $addCaractLink.on('click', function(e) {
        e.preventDefault();
        addCaractForm(collectionHolderCaract, $addCaractLink);
    });

    collectionHolderConcept.find('li.concept').each(function() {
        addConceptFormDeleteLink($(this));
    });		
	
    collectionHolderConcept.append($addConceptLink);

    collectionHolderConcept.data('index', collectionHolderConcept.find(':input').length);

    $addConceptLink.on('click', function(e) {
        e.preventDefault();

        addConceptForm(collectionHolderConcept, $addConceptLink);
    });
});


function addCaractForm(collectionHolder, $newLinkLi) {
    var prototype = collectionHolder.data('prototype');

    var index = collectionHolder.data('index');

    var newForm = prototype.replace(/__name__/g, index);

    collectionHolder.data('index', index + 1);

    var $newFormLi = $('<li class="caract"></li>').append(newForm);
    $newLinkLi.before($newFormLi);
    addCaractFormDeleteLink($newFormLi);
}


function addCaractFormDeleteLink($caractFormLi) {
    var $removeFormA = $('<a href="#" class="btn btn-primary">delete this caract</a>');
    $caractFormLi.append($removeFormA);

    $removeFormA.on('click', function(e) {
        e.preventDefault();

        $caractFormLi.remove();
    });
}



function addConceptForm(collectionHolder, $newLinkLi) {
    var prototype = collectionHolder.data('prototype');

    var index = collectionHolder.data('index');

    var newForm = prototype.replace(/__name__/g, index);

    collectionHolder.data('index', index + 1);

    var $newFormLi = $('<li></li>').append(newForm);
    $newLinkLi.before($newFormLi);
    addConceptFormDeleteLink($newFormLi);
}


function addConceptFormDeleteLink($conceptFormLi) {
    var $removeFormA = $('<a href="#" class="btn btn-primary">delete this concept</a>');
    $conceptFormLi.append($removeFormA);

    $removeFormA.on('click', function(e) {
        e.preventDefault();
        $conceptFormLi.remove();
    });
}
