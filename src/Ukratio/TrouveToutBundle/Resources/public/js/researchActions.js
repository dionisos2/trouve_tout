function select_all() {
	$('[id*=selection_id_]').prop("checked", true);
}

function unselect_all() {
	$('[id*=selection_id_]').prop("checked", false);
}
