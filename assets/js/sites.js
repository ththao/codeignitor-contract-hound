$('.confirm-delete').click(function () {
	sSiteUrl = $('.site-url',$(this).parent().parent()).text();
	return confirm('Are you sure you want to delete: '+sSiteUrl+'?  All data for this site will be lost.');
});
