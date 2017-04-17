(function ($, Drupal, drupalSettings) {

    console.log(drupalSettings);
    var pubKey = drupalSettings.donation_stripe.key;
    var formId = '#' + drupalSettings.donation.form_id;

    Stripe.setPublishableKey(pubKey);

    // This function is just used to display error messages on the page.
    // Assumes there's an element with an ID of "payment-errors".
    function reportError(msg) {
        // Show the error in the form:
        $('#payment-errors').text(msg).addClass('alert alert-error');
        // re-enable the submit button:
        $('#edit-submit').prop('disabled', false);
        return false;
    }

    $(document).ready(function() {


        // Watch for a form submission:
        $(formId).submit(function(event) {

            $('#edit-submit').prop('disabled', true);

            // // Get the values:
            // var ccNum = $('.card-number').val(),
            //     cvcNum = $('.card-cvc').val(),
            //     expMonth = $('.card-expiry-month').val(),
            //     expYear = $('.card-expiry-year').val();
            //
            // // Validate the number:
            // if (!Stripe.card.validateCardNumber(ccNum)) {
            //     error = true;
            //     reportError('The credit card number appears to be invalid.');
            // }
            //
            // // Validate the CVC:
            // if (!Stripe.card.validateCVC(cvcNum)) {
            //     error = true;
            //     reportError('The CVC number appears to be invalid.');
            // }
            //
            // // Validate the expiration:
            // if (!Stripe.card.validateExpiry(expMonth, expYear)) {
            //     error = true;
            //     reportError('The expiration date appears to be invalid.');
            // }

            // Validate other form elements, if needed!

            Stripe.source.create({
                type: 'card',
                card: {
                    number: $('.card-number').val(),
                    cvc: $('.card-cvc').val(),
                    exp_month: $('.card-expiry-month').val(),
                    exp_year: $('.card-expiry-year').val(),
                },
            }, stripeResponseHandler);

            // Prevent the form from submitting:
            return false;

        }); // form submission
    }); // document ready.

    // Function handles the Stripe response:
    function stripeResponseHandler(status, response) {

        var $form = $(formId);

        // Check for an error:
        if (response.error) {

            // Show the errors on the form
            $form.find('#payment-errors').text(response.error.message);
            $form.find('#edit-submit').prop('disabled', false); // Re-enable submission

        } else { // No errors, submit the form:

            // Get the source ID:
            var source = response.id;

            // Insert the token into the form so it gets submitted to the server
            //f.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
            $('.stripe-token').val(source);

            // Submit the form:
            $form.get(0).submit();

        }

    } // End of stripeResponseHandler() function.

})(jQuery, Drupal, drupalSettings);
