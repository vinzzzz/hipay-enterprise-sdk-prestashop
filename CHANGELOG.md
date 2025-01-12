# Version 2.7.1

- Get payment method configuration from PHP SDK
- Add update notifications in admin dashboard

# Version 2.7.0

- Add MyBank Payment method
- Add Italian translations
- Fix: Handle wrapping gift in order with basket
- Fix: upgrade management
- Fix: capture form not displaying
- Fix: french and english translations

# Version 2.6.1

- Fix: shipping fees calculation

# Version 2.6.0

- Add error message on field (hosted fields)
- Fix: device fingerprint on hosted fields
- Update payment method configuration
- Fix: credit card number format on paste

# Version 2.5.2

- Fix: Bnppf with Prestashop 1.6

# Version 2.5.1

- Fix : Category and carrier mapping not saving

# Version 2.5.0

- Add one-click support for Hosted Page
- Refactoring one-click workflow
- Fix amex one-click
- Add minimum prestashop support 

# Version 2.4.1

- Fix missing month in credit card form

# Version 2.4.0

- Get payment product from SDK JS
- Configurable SDK JS url
- Fix: amount in basket on maintenance request
- Fix: Oney payment method
- Fix: cardholder For Amex

# Version 2.3.2

- Fix : Refund and capture with an update of product price or discount

# Version 2.3.1

- Fix : refund for local payment method
 
# Version 2.3.0

- Add support for hosted fields
- Fix : switch klarna to klarnainvoice 

# Version 2.2.7

- Fix : unnecessary mandatory CVV for Maestro card

# Version 2.2.6

- [#64](https://github.com/hipay/hipay-enterprise-sdk-prestashop/issues/64) Fix issue [#64] 
- Remove electronic signature from SDD
- Fix : Js error on backend notification pop-up

# Version 2.2.5

- Improve CI 
- Refactor functional tests
- Fix : Proxy settings

# Version 2.2.4

- Fix : Bug on BCMC card form

# Version 2.2.3

- Fix : Bug on notification

# Version 2.2.2

- Fix : Credit card form autofill not filling properly
- Fix : Bug with discount in basket (PHP7.1 silent conversion error)

# Version 2.2.1

- Fix : update Mastercard bin range
- Fix : Add message to specify that Oneclick payment can only be used with Api mode

# Version 2.2.0

- Add Oney gift card payment support
- Add support for several hashing algorithm for notification
- Add support for notify_url

# Version 2.1.5

- Fix: Fix Js error

# Version 2.1.4

- Fix: credit card block are displaying even if no credit card payment are activated

# Version 2.1.3

- Fix local payment with hosted order

# Version 2.1.2

- Fix translations

# Version 2.1.1

- Fix error 500 on refused notification

# Version 2.1.0

- Fix link in PrestaShop BO (Module configuration page)
- Add upgrade script
- Fix Oneclick payment bug on 1.6 and 1.7

# Version 2.0.5

- Add payment method **Bnp personal Finance**
- Add log for concurrent transactions
- Override "OrderExists" method for concurrent notifications (Bug with prestashop cache)  
- Add translation support for payment method name in front-office. 
- Add FAQ translation
- Change form handling for passphrase

# Version 2.0.4

- Fix Product key for Prestashop addons

# Version 2.0.3

- Fix template FAQ Tab

# Version 2.0.2

- Fix translations FR and EN

# Version 2.0.1-beta

- Fix refund with multi currencies
- Fix module installation 
 
# Version 2.0.0-beta

- Project initialization
