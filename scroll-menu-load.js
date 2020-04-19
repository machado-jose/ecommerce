window.addEventListener("load", function(event) {
    let position = $(".scroll-menu-custom").offset().top;
    $("html, body").animate({
    	scrollTop: position +"px"
    }, 1000);
});