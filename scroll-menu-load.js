window.addEventListener("load", function(event) {
	if($(".scroll-menu-custom").length > 0)
	{
		let position = $(".scroll-menu-custom").offset().top;
	    $("html, body").animate({
	    	scrollTop: position +"px"
	    }, 1000);
	}  
});