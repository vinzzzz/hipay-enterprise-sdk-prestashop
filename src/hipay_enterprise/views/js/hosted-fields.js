$(document).ready(function () {
    $("#card-number").focus(function () {
        $('#radio-no-token').prop('checked', true);
    });

    $('#radio-no-token').change(function () {
        $('#credit-card-group').collapse('show');
    });

    $('.radio-with-token').change(function () {
        $('#credit-card-group').collapse('hide');
    });

    $("#tokenizerForm").submit(function (e) {
        var form = this;
        // prevent form from being submitted
        e.preventDefault();
        e.stopPropagation();

        if (myPaymentMethodSelected) {
            if (isOneClickSelected()) {
                oneClickSelected(form);
                return true; // allow whatever action that would normally happen to continue
            }

            hipayHF.getPaymentData()
                .then(function (response) {
                        if (isCardTypeOk(response)) {
                            displayLoadingDiv();
                            afterTokenization(response);
                            //submit the form
                            form.submit();
                            return true;
                        } else {
                            $("#error-js").show();
                            $("#error-js").text(activatedCreditCardError);
                            return false;
                        }
                    },
                    function (errors) {
                        handleErrorhipayHF(errors);
                    }
                );
        }
    });
});
var hipayHF;

document.addEventListener('DOMContentLoaded', initHostedFields, false);

function initHostedFields() {

    var hipay = HiPay({
        username: api_tokenjs_username,
        password: api_tokenjs_password_publickey,
        environment: api_tokenjs_mode,
        lang: lang
    });

    var config = {
        selector: "hipayHF-container",
        multi_use: oneClick,
        fields: {
            cardHolder: {
                selector: "hipayHF-card-holder",
                defaultFirstname: cardHolderFirstName,
                defaultLastname: cardHolderLastName,
            },
            cardNumber: {
                selector: "hipayHF-card-number"
            },
            expiryDate: {
                selector: "hipayHF-date-expiry"
            },
            cvc: {
                selector: "hipayHF-cvc",
                helpButton: true,
                helpSelector: "hipayHF-help-cvc"
            }
        },
        styles: {
            base: style.base
        }
    };

    hipayHF = hipay.create("card", config);

    hipay.injectBaseStylesheet();

    hipayHF.on("blur", function (data) {
        // Get error container
        var domElement = document.querySelector(
            "[data-hipay-id='hipay-card-field-error-" + data.element + "']"
        );

        // Finish function if no error DOM element
        if (!domElement) {
            return;
        }

        // If not valid & not empty add error
        if (!data.validity.valid && !data.validity.empty) {
            domElement.innerText = data.validity.error;
        } else {
            domElement.innerText = '';
        }
    });

    hipayHF.on("inputChange", function (data) {
        // Get error container
        var domElement = document.querySelector(
            "[data-hipay-id='hipay-card-field-error-" + data.element + "']"
        );

        // Finish function if no error DOM element
        if (!domElement) {
            return;
        }

        // If not valid & not potentiallyValid add error (input is focused)
        if (!data.validity.valid && !data.validity.potentiallyValid) {
            domElement.innerText = data.validity.error;
        } else {
            domElement.innerText = '';
        }
    });

}

function handleErrorhipayHF(errors) {

    for (var error in errors) {
        var domElement = document.querySelector(
            "[data-hipay-id='hipay-card-field-error-" + errors[error].field + "']"
        );

        // If DOM element add error inside
        if (domElement) {
            domElement.innerText = errors[error].error;
        }
    }
}
