(function($){
	$.fn.extend({
		donetyping: function (callback,timeout) {
//			timeout = timeout || 1e3; // 1 second default timeout
			timeout = timeout || 100
			var timeoutReference,
				doneTyping = function(el) {
					if (!timeoutReference) return;
					timeoutReference = null;
					callback.call(el);
				};
			return this.each (function (i,el) {
				var $el = $(el);
				// Chrome Fix (Use keyup over keypress to detect backspace)
				// thank you @palerdot
				$el.is(':input') && $el.on('keyup keypress',function(e) {
					// This catches the backspace button in chrome, but also prevents
					// the event from triggering too premptively. Without this line,
					// using tab/shift+tab will make the focused element fire the callback.
					if (e.type=='keyup' && e.keyCode!=8) return;

					// Check if timeout has been set. If it has, "reset" the clock and
					// start over again.
					if (timeoutReference) clearTimeout(timeoutReference);
					timeoutReference = setTimeout(function() {
						// if we made it here, our timeout has elapsed. Fire the
						// callback
						doneTyping(el);
					}, timeout);
				}).on('blur',function() {
					// If we can, fire the event since we're leaving the field
					doneTyping(el);
				});
			})
		}
	});
})(jQuery);

function loadBooks (pageData) {
	$('.book-list').html(loading);
	if (pageData) pageData = pageData.split('?')[1];
	else pageData = '';
	var formData = pageData+'&'+$('#formFilter').serialize()+'&'+$('#bSearch').serialize();
	console.log(formData);
	$.ajax({
		url: "?do=filter",
		type: "POST",
		data: formData,
		success: function (data) {
			$('#book-list').html(data);
			control()
		}
	});
}

function control () {
	flatApp();
	$('.pagination li:not(".disabled") a:not(".disabled,.active")').click(function () {
		loadBooks($(this).attr('href'));
		return false
	});
}

$(document).ready(function () {
	loadBooks();
	$('#formFilter').submit(function () {
		loadBooks();
		return false
	});
	$('#formFilter').find('input,select').on('change', function () {
	}).donetyping(function () {
		loadBooks();
	});
	$('#records-per-page').on('change', function () {
		loadBooks();
	});
	$('[name="avai"]').change(function () {
		var v = $(this).val();
		if (v == 1) {
			$('.select-bookid+.chosen-container').show()
		} else {
			$('.select-bookid+.chosen-container').hide()
		}
	})
})
