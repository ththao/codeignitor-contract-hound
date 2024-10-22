$('.confirm-delete').click(function () {
	sEmail = $('.email',$(this).parent().parent()).text();
	return confirm('Are you sure you want to delete: '+sEmail+'?  All data for this site will be lost.');
});
