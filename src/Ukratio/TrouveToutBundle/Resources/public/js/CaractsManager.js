function CaractsManager() {
	DynamicFormsManager.call(this, 'caracts', 'caract', 'Ajouter une caractéristique', 'Supprimer la caractéristique');
}

$.extend(CaractsManager.prototype, DynamicFormsManager.prototype);

