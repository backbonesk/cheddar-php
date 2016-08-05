# Changelog

## v0.4.3: 05/08/2016

Added support for sandbox mode with available services and switching sandbox and production URLs to [cheddarpayments.com](https://www.cheddarpayments.com).

## v0.4.2: 13/07/2016

Updated documentation to reflect added ability to refund Tatra banka's CardPay payments.

## v0.4.1: 13/07/2016

Small fix to update metadata instead of refunds, when updating a payment.

## v0.4.0: 12/07/2016

Open sourcing Cheddar library, open sourcing it with added support for Poštová banka's iTerminal service.

Library has been rewritten to support PSR-2 standard.

## v0.3.0: 01/03/2016

Return card expiration date if available in `card_expire_on` instance variable in `Payment` data object.

## v0.2.3: 15/10/2015

Fixed minor annoyance with warnings when ran as part of command line script.

## v0.2.2: 09/09/2015

Fixed an issue with validating messages and incorrect expectations of signatures.

## v0.2.1: 07/09/2015

Updated `chaching` dependency to add support for PayPal service and also support for ČSOB and a couple other Czech banks via GP webpay service.

## v0.2.0: 03/04/2015

Preparations to allow for specific instances of Cheddar service.

## v0.1.7: 28/03/2015

Bugfix release due to incorrect signing of all requests.

## v0.1.6: 28/03/2015

Send `X-Real-IP` header when performing requests to allow Cheddar to save user's IP address.

## v0.1.5: 26/03/2015

Fixed issue with incorrectly assigning optional values in `Payment` data object.

## v0.1.4: 15/03/2015

Removed unused `chaching` dependency and added support for `cancelled` payment status added to Cheddar service.

## v0.1.3: 29/01/2015

Updated `chaching` dependency to add VÚB eCard support.

## v0.1.2: 26/01/2015

Minor tweaks to the documentation and a horrible error in configuration.

## v0.1.1: 25/01/2015

Minor tweaks to the documentation.

## v0.1.0: 25/01/2015

Initial version with support for creating payments and checking their details afterwards.
