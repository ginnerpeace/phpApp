$(function() {
	if (document.domain != 'onmpw.com' && document.domain != 'www.onmpw.com'){
	    window.location.href='http://www.onmpw.com';
	}
	if(top.location != self.location)top.location=self.location;
	$(window).scroll(function(event) {
		var num=$(window).scrollTop();
		var m=115
		if (num>m) {
			$('.nav').css({
				position: 'fixed',
				top:0,
				zIndex:1000
			});
		}else{
			$('.nav').css({
				position:'static'
				})
		};
	});
});