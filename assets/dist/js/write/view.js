$(document).ready(function () {
	var chapters = $('table#books_chapters').DataTable({
		"ordering": false,
//		"order": [[1, 'asc']],
		"pageLength": 5,
		"lengthMenu": [5, 15, 50, 100]
	});
	if ($('table#books_publisher').length) {
		var publishers = $('table#books_publisher').DataTable({
			"order": [[2, 'desc']]
		});
	} else if ($('table#books_request_publish').length) {
		var requests = $('table#books_request_publish').DataTable({
			"order": [[0, 'asc']]
		});
	}
	$('.book-des .box-body').each(function () {
		var lines = $(this).html().split(/\<br\>|\<br\/\>|\<br\>\<br\/\>/);
		$(this).html('<p>' + lines.join("</p><p>") + '</p>');
	});
	$('.book-des p').each(function () {
		if ($(this).html() == '&nbsp;') $(this).remove();
	});
	// report link
	$('.report-link a').click(function () {
		popup_page($(this).attr('href'));
		return false
	});
	// register
	$('.borrow-register').click(function () {
		console.log('?do=register');
		$.post('?do=register', function (response) {
			var meUname = $('#top_navbar .myID').attr('id');
			if (response == 1) { // success register
				mtip('', 'success', 'Thành công!', 'Yêu cầu của bạn đã được thêm vào hàng đợi. Chúng tôi sẽ liên hệ với bạn khi có sách.');
				$('.borrow-register').removeClass('btn-success').addClass('btn-danger').html('Hủy đăng kí đặt sách');
				$('.book-borrow-list').append('<li class="book-borrow-one"><a href="'+MAIN_URL+'/user/'+meUname+'"><img class="book-borrow-one-avt" src="'+$('.nav-users .avatar').attr('src')+'"> '+$('.nav-users .s-title').text()+'</a><span class="borrow-status" data-stt="0" title="Đang đợi đến lượt"></span> </li>')
			} else if (response == 2) {
				mtip('', 'success', 'Thành công!', 'Yêu cầu của bạn đã được xóa.');
				$('.book-borrow-one[data-u="'+meUname+'"]').remove();
				$('.borrow-register').removeClass('btn-danger').addClass('btn-success').html('Đăng kí đặt sách');
			} else {
				mtip('', 'error', 'Lỗi!', 'Có lỗi khi thực hiện yêu cầu. Vui lòng liên hệ với ban quản trị để được hỗ trợ thêm.');
			}
		});
		return false
	})
})
