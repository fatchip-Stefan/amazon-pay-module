# Change Log for OXID eSales Amazon Pay

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [2.1.3] - Release

### Fixed
* Delivery address was not send to Amazon

## [2.1.2] - Release

### Fixed

* Compatibility-Issue with Other themes like Flow
* [0007475](https://bugs.oxid-esales.com/view.php?id=7475) Compatibility-Issue with OXID 6.1
* [0007471](https://bugs.oxid-esales.com/view.php?id=7475) Compatibility-Issue with OXID 6.1
* [0007462](https://bugs.oxid-esales.com/view.php?id=7475) if mandatory Confirm General Terms and Conditions field is activated -> you go round in circles

## [2.1.1] - Release

### Fixed

* [0007471](https://bugs.oxid-esales.com/view.php?id=7471) Module throws an exception in case the button Sign off is used during the checkout
* [0007455](https://bugs.oxid-esales.com/view.php?id=7455) if Amazon Pay module is active, then the shipping address is ignored for all payment methods
* [0007462](https://bugs.oxid-esales.com/view.php?id=7462) if mandatory Confirm General Terms and Conditions field is activated -> you go round in circles
* compatibility issues with core or other payment-modules 

## [2.1.0] - Release

### Added
 
* added additional payment button
* If the order or individual items are canceled or deleted, Amazon will issue a refund or cancel
* Added log in via Amazon
* Added partial refund

### Fixed

* Fix bug showing of maintenance mode in admin panel after refund
* [0007462](https://bugs.oxid-esales.com/view.php?id=7462) mandatory Confirm General Terms and Conditions field
* [0007463](https://bugs.oxid-esales.com/view.php?id=7463) reAdd missing MerchantReferenceId
* [0007350](https://bugs.oxid-esales.com/view.php?id=7350) OXTRANSID contains authorize status codes

## [2.0.1] - Release 

### Added

* update template to use Amazon Pay Express
* extends functionality of Payload objects
* add methods `ViewConfig::setArticlesId(string)`, `ViewConfig::getPayloadExpress()`, `ViewConfig::getSignature()`

### Fixed

* [0007345](https://bugs.oxid-esales.com/view.php?id=7345) Refunds are not booked
* [0007369](https://bugs.oxid-esales.com/view.php?id=7369) Delay in the response when amazon pay button is clicked
* [0007368](https://bugs.oxid-esales.com/view.php?id=7368) Declined message is not shown when buyer selects the declined simulation code card
* [0007371](https://bugs.oxid-esales.com/view.php?id=7371) Amazon pay button is missing from the product/basket page after the previous order is placed successfully
* [0007370](https://bugs.oxid-esales.com/view.php?id=7370) Emails are not shared to buyer after successful placement of order
* [0007379](https://bugs.oxid-esales.com/view.php?id=7379) Error messages from the DispatchController spam the log, probably a template is missing here
* [0007351](https://bugs.oxid-esales.com/view.php?id=7351) PlatformId set in headers instead of Payload

## [2.0.0] - Release with new Namespace

* we change the namespace from OxidProfessional (oxps) to OxidSolutionCatalysts (osc)
* please read the documentation for the upgrade from v1.2.0 to v2.0.0
* rename method `ViewConfig::isCompatibleTheme()` as  `ViewConfig::isThemeBasedOn()`

## [1.2.0] - Technical Release

* simplify the template structure (remove theme param from metadata, add switch within the templates)
* change folder structure of module fir better testing
* refresh default-amazon-countries
* dont destroy session basket if you failed by clicking the amazon button with an existing user-account
* some session tweaks for better consumer experience during checkout
* us github Actions for testing the module

## [1.1.3] - Maintenance Release

* faster checkout
* Currency restriction may prevent Amazon Payment
* No Country restriction allows delivery in all Amazon Countries
* remove Validation Hack
* use central constante for paymentid and deladr

## [1.1.2] - Backward-Compatibility for OXID 6.1

* Add Backward-Compatibility for OXID 6.1
* Add Secure use of OrderController::execute
* Fallback InvoiceAddress (With the Amazon button (without OXID login) we use the billing address
  from Amazon. However, if this does not correspond to the shop countries, we fall back on the
  Amazon delivery address as the billing address, since the delivery addresses have already been
  restricted by country beforehand)

## [1.1.1] - Maintenance Release

* change handling of required fields

## [1.1.0] - First Release for OXID 6.2

* change the module-id to oxps_amazonpay
* configuration-handling OXID6.2 compatible

## [Undecided] - unreleased

## [1.0.0] - First Release for OXID 6.1
