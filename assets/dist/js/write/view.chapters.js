var table = $('table#r_comments').DataTable({
	"ajax": '?do=getCommentsChapter',
	"ordering": false,
//	"order": [[0, 'asc']], // order by time asc
	"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
		$(nRow).attr("id", aData[1]);
		return nRow;
	},
	"aoColumns": [
		{ "sClass": "hidden" },
		{ "sClass": "hidden" },
		{ "sClass": "one-comt", "sValign": "top" }
	],
	"initComplete": function (settings, json) {
//		console.log(json.data);
		if (json.data.length > 0) {
			$('.r-cmts').show();
			var p = window.location.href.split('#')[1];
			if (p) {
				table.row(Number(p)-1).scrollTo(false);
			}
			setInterval(function () {
				table.ajax.reload(function (json) {
					// do something?
				}, false);
			}, 100000);
		} else $('.r-cmts').hide();
	}
});

(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8&appId=1644510825775192";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

$(document).ready(function () {
	var chapters = $('table#books_chapters').DataTable({
		"order": [[1, 'asc']],
		"pageLength": 25,
		"lengthMenu": [25, 50, 100, 200]
	});

	rate(".ratings-form");
})
