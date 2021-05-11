jQuery(function($) {
	$('table.table * .dropdown-menu').parent().on('shown.bs.dropdown', function() {
		let menu = jQuery(this).find('.dropdown-menu');
		let position = menu.offset();
		position.top -= document.documentElement.scrollTop;
		position.left -= document.documentElement.scrollLeft;
		menu.css('position', 'fixed').css(position);
	}).parent().on('hidden.bs.dropdown', function() {
		jQuery(this).find('.dropdown-menu').css('position', 'absolute').css('left', '0px').css('top', '0px');
	});
});