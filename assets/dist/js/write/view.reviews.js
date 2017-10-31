$(document).ready(function () {
	var reviews = $('table#book-reviews').DataTable({
		"order": [[0, 'desc']],
		"pageLength": 25,
		"lengthMenu": [25, 50, 75, 100]
	});
})