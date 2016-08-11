function refreshCart()
{
	$.ajax({
		url: '{{ path("store_cart_nav") }}',
		type: "GET",
		data: { 
		},
		success: function(data) {
			$('.cart-nav').replaceWith(data);
		}
	}); 
}


$('body').on("submit", ".ajaxform", function (e) {
	e.preventDefault();
	$this = $(this).closest("form")
	$.ajax({
		cache: false,
		url: $this.attr("action"),
		data: $this.serialize(),
		type: 'GET',
		success: function (data) {
			refreshCart();
		}
	})
});
