FrontendCore.define('form-promotions-with-timer', [], function () {
	return {
		onStart: function () {

			var self = this,
				sTypeValue = document.getElementById('elcodi_admin_promotions_with_timer_form_type_type').value;

			FrontendTools.trackEvent('JS_Libraries', 'call', 'form-promotions-with-timer');

			self.TypeVisibility(sTypeValue);

			$('#elcodi_admin_promotions_with_timer_form_type_type').change( function(){
				self.TypeVisibility( this.value );
			});

		},
		TypeVisibility: function (sValue) {

			var ShowElement,
				hideElement,
				sNameFixed = 'fixed-amount',
				sNamePercent = 'percent-amount';

			if (sValue === '1') {
				ShowElement = sNameFixed;
				hideElement = sNamePercent;
			} else {
				hideElement = sNameFixed;
				ShowElement = sNamePercent;
			}

			$('#' + ShowElement).slideDown();
			$('#' + hideElement).slideUp();

		}
	};
});

