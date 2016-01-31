$(document).ready(function() {
	$(".masterTooltip").hover(function() {
		var title = $(this).attr('title');
		$(this).data('tipText', title).removeAttr('title');
		$('<p class="tooltip"></p>').html(title).appendTo('body').fadeIn('200');
	}, function() {
		$(this).attr('title', $(this).data('tipText'));
		$(".tooltip").remove();
	}).mousemove(function(e) {
		var mouseX = e.pageX + 20;
		var mouseY = e.pageY + 10;
		var tipHeight = $('.tooltip').height() + 20;
		var tipWidth = $('.tooltip').width() + 20;
		if(mouseX + tipWidth > $(window).width())
			mouseX = e.pageX - 20 - tipWidth;
		if(mouseY + tipHeight > $(window).height())
			mouseY = e.pageY - 10 - tipHeight;
		$('.tooltip').css({ top: mouseY, left: mouseX })				
	});
});
