# Cheddar Bindings for PHP

A PHP wrapper for Cheddar's application interface. Cheddar is a payment gateway for processing and executing transactions within a neat and universal API.

At the moment, the following payment methods and services are supported by Cheddar and this library:

* [CardPay](https://www.tatrabanka.sk/sk/business/ucty-platby-karty/elektronicke-bankovnictvo/cardpay.html) with optional addition for [ComfortPay](http://www.tatrabanka.sk/cardpay/CardPay_ComfortPay_technicka_prirucka.pdf) service – Tatra banka, a.s.
* [TatraPay](http://www.tatrabanka.sk/sk/business/ucty-platby-karty/elektronicke-bankovnictvo/tatrapay.html) – Tatra banka, a.s.

* [ePlatby VÚB](https://www.vub.sk/pre-podnikatelov/nonstop-banking/e-commerce-pre-internetovych-obchodnikov/e-platby-vub/) – VÚB, a.s.

* [VÚB eCard](http://www.vub.sk/pre-firmy/nonstop-banking/e-commerce-pre-internetovych-obchodnikov/ecard/) – VÚB, a.s.

* [SporoPay](https://www.slsp.sk/sk/biznis/ucty-a-transakcie/prijimanie-platieb-kat/Sporopay) – Slovenská sporiteľna, a.s.

* [iTerminal](https://www.postovabanka.sk/pre-firmy/eft-pos-terminal/iterminal/) – Poštová banka, a.s.

* [GP webpay](http://gpwebpay.cz/Content/downloads/GP_webpay_Seznameni_se_systemem_072013.pdf) – Global Payments Europe, s.r.o.

* [PayPal](http://www.paypal.com) – PayPal (Europe) S.à r.l. et Cie, S.C.A.

To see what is new or changed in the current version, check out the [changelog](./CHANGELOG.md).

## Requirements

Cheddar requires PHP version 5.4.0 or greater (including PHP 7) with `json`, `hash` and `cURL` extensions installed.

If you use Composer, these dependencies should be handled automatically. If you install manually, you'll want to make sure that these extensions are available.

## Setup and installation

### Composer
The recommended way to install the library is to use [Composer](http://getcomposer.org/) and add it as a dependency to your project's `composer.json` file.

```bash
composer require backbone/cheddar
```

Then to use the bindings, use Composer's autoload:

```php
require_once('vendor/autoload.php');
```

### Manual installation

If you do not wish to use Composer, you can download the [latest release](https://github.com/backbonesk/cheddar-php/releases). Then, to use the library, include the `init.php` file.

```php
require_once('/path/to/cheddar-php/init.php');
```


## Usage

First off, you need to require the library and provide authentication information by providing your user handle and shared secret you got.

```php
$client = new \Cheddar\Cheddar([
	'key' => 'TEST',
	'secret' => '00000000000000000000000000000000'
]);
```

If you need to access an environment other than production or are running custom instance of Cheddar service, you can set the endpoint manually.

```php
$client->apiEndpoint('https://...');
```

And if you just want to use the sandbox version of default Cheddar instance, run the following command.

```php
$client->sandbox(true);
```

**Please note** that only VÚB eCard, iTerminal, GP webpay and PayPal currently allow for using their test environments so in case of other providers production URLs will be used! When using sandbox with supported bank or financial institution never use real world credit cards / accounts for testing payment methods implementation (they will not work). Always use virtual testing cards / accounts provided for this purpose by the payment institution.

### Creating a transaction

It is quite simple to instantiate a payment.

Here’s a quick piece of example code to get you started which will call the Cheddar service and retrieve UUID – universal identifier of the transaction and set the transaction status to `none` (see next section for more on transaction statuses). User's IP is automatically added to the data array before it is sent to the service.

```php
$payment = $client->payment()->create(
    \Cheddar\Cheddar::SERVICE_CARDPAY, [
        'amount'           => 9.99,
        'currency'         => \Cheddar\Currencies::EUR,
        'variable_symbol'  => '1000000000',
        'payer_name'       => 'John Doe',
        'description'      => 'my first test payment',
        'payer_email'      => 'john@doe.com',
        'return_url'       => 'https://my-test-server.dev',
        'notification_url' => 'https://my-test-server.dev',
    ]
);
```

First argument is a service provider, which can currently be one of the following:

| Service name | Description |
|:-------------|:------------|
|`Cheddar::SERVICE_SPOROPAY`|SporoPay, Slovenská sporiteľňa|
|`Cheddar::SERVICE_TATRAPAY`|TatraPay, Tatra banka|
|`Cheddar::SERVICE_CARDPAY`|Cardpay, Tatra banka|
|`Cheddar::SERVICE_EPLATBY`|ePlatby, VÚB|
|`Cheddar::SERVICE_ECARD`|eCard, VÚB|
|`Cheddar::SERVICE_PAYPAL`|PayPal Payments Standard, PayPal|
|`Cheddar::SERVICE_GPWEBPAY`|GP webpay, Global Payments Europe|
|`Cheddar::SERVICE_ITERMINAL`|iTerminal, Poštová banka|

Second argument to the function call is an associative array of configuration options. Which options have to be used and which have no effect at all depends on the service provider. The next table lists all possible attributes:

| Attribute name | Data type | Required? | Notes |
|:---------------|:---------:|:---------:|:------|
|`amount`|float|✓|amount required in the specified currency|
|`currency`|string|✓|currency code or numeric code according to [ISO 4217](http://www.iso.org/iso/home/standards/currency_codes.htm)|
|`variable_symbol`|string|✓| |
|`constant_symbol`|string| | |
|`card_id`|string| |optional card token in periodical payments<br>_applicable only to ComfortPay transactions_|
|`payer_name`|string|✓|customer’s name|
|`payer_email`|string|✓|customer’s email (which has to be a valid e-mail address)|
|`language`|string| |customer’s language|
|`description`|string| |reference for the customer|
|`return_url` or `callback`|string|✓|URL to return to after the payment<br>_iTerminal does not use this attribute since you have to set this up once for all transactions in their administration interface_|
|`notification_url`|string| |URL to send notifications to<br>_required for PayPal transactions_|
|`cpp_logo_image`|string| |header image at PayPal<br>_applicable only to PayPal transactions_|
|`cpp_cart_border_color`|string| |HEX code of colour at PayPal<br>_applicable only to PayPal transactions_|
|`periodicity`|integer| |periodicity in days, when the next periodical payment will be automatically executed; default value is 30 days<br>_applicable only to ComfortPay transactions_|

Note that all of the supported currencies are available as a simple constant on `\Cheddar\Currencies` class to make it easier in code.

After the call you can inspect the returning `\Cheddar\Data\Payment` object, which is described in the `Getting transaction details` part of this document.

To get to the URL of a payments gateway at the bank where the payment is processed just call the following:

```php
header('Location: ' . $payment->redirectUrl());
```

After the payment process at the payment gateway is finished, you will be redirected to the URL you specified in `return_url` / `callback` parameter during the $client->payment()->create(...) call in the example above. The URL will have two more GET parameters added - `uuid`, for the payment identifier and `status`, for the current status of the payment transaction (for some payment methods this may change in time, and you will be notified about the change to the URL you specified in the `notification_url` parameter [see the `Asynchronous transaction notifications` part of this document for more info])

#### Allowed transaction statuses

|Status name|Description|
|:----------|:----------|
|`none`|transaction has been created, but the user has not been redirected to bank’s payment gateway|
|`in_progress`|user has been redirected to bank’s payment gateway|
|`completed`|transaction has been successfully completed|
|`denied` or `rejected`|transaction has been rejected by the bank (the most usual reasons include user error in inputting card details and insufficient funds)|
|`timeout`|special temporary status used only by Tatra banka’s TatraPay service|
|`pending`|special status for PayPal before an IPN notification has resolved the transaction status as either rejected or completed|
|`cancelled`|in case of periodical payments available only with Tatra banka’s ComfortPay service this status means that planned transaction has been cancelled|
|`expired`|old payment without clear result (e.g. user abandoned the payment form while at bank’s gateway)|

### Getting transaction details

To get all details of an existing payment transaction simply pass the UUID of the payment to the following method:

```php
$payment = $client->payment()->details($uuid);
```

Afterwards you can inspect the returning `\Cheddar\Data\Payment` object, which contains these properties:

|Property name|Data type|Always present?|Default value|Notes|
|:------------|:-------:|:-------------:|:-----------:|:----|
|`uuid`|string|✓| | |
|`status`|string|✓|none|current transaction status (see the next table)|
|`variable_symbol`|string|✓| |the same as was sent while creating  the payment object (see previous section)|
|`constant_symbol`|string|✓|0308|payment for services|
|`amount`|float|✓|0.00|original amount of the transaction|
|`refunded_amount`|float|✓|0.00|refunded amount of the transaction|
|`service_fee_amount`|float|✓|0.00|provision for the transaction in card payments _available when using CardPay / ComfortPay and set up with PGP encrypted statements or PayPal service_|
|`currency`|`Currencies`|✓|EUR|currency of the transaction|
|`periodicity`|integer| |0|number of days in which next payment will be executed|
|`periodicity_no`|integer| |1|number of transaction in order (using the same variable symbol)|
|`charge_on`|`Datetime`| | |when was or should be this transaction executed|
|`card_expire_on`|`Datetime`| |null|date of card expiration _available only when using ComfortPay or VÚB eCard service_|
|`card_no`|string| | |masked card number _available only when using ComfortPay or VÚB eCard service_|
|`transaction_identifier`|string| | |internal transaction identifier of the bank _available only when using CardPay / ComfortPay, TatraPay or VÚB eCard service_|

### Asynchronous transaction notifications

Transactions may have a `notification_url` attribute (in case of PayPal and ComfortPay the attribute is mandatory), that will receive a ping on every change to a transaction (in case of PayPal or ComfortPay it is also the only way to find out the status of the payment).

Cheddar calls the value of `notification_url` attribute as POST request with GET attributes `uuid` and `signature` (which needs to be verified) and `application/json` body with full payment details as explained in the previous section.

To validate the signature, you need to call the following:

```php
$client->message()->validate(
    $_GET['uuid'], $_GET['signature']
);
```

In case the signature is incorrect a `\Cheddar\Exceptions\MessageIntegrityException` exception is thrown, otherwise the function returns `true`. After a successful validation you can trust the json-encoded body of the request.

The json-encoded body will look something like this:

```
{
    "uuid": "b1fcc76a-d284-4cbc-bce9-b415dc973763",
    "service": {
        "handle": "cardpay",
        "provider": "Tatra banka, a.s.",
        "name": "CardPay"
    },
    "status": {
        "status": "completed",
        "description": "The payment has been approved by the bank or financial institution"
    },
    "variable_symbol": "1000000000",
    "constant_symbol": "0308",
    "amount": 9.99,
    "refunded_amount": 0,
    "service_fee_amount": 0,
    "currency": {
        "alpha_code": "EUR",
        "numeric_code": 978,
        "name": "Euro"
    },
    "periodicity": 0,
    "periodicity_no": 1,
    "created_at": "2018-12-01 10:34:26",
    "events": [],
    "note": "my first test payment",
    "card_no": "****************",
    "transaction_identifier": "Aq83Lys6WHdiP8TFo6pnkRvTlpC="
}
```

### Updating planned transaction

The next use case is the ability to change date and / or amount of a next planned playment. The output of the call is summary of the planned payment including its UUID.

```php
$payment = $client->payment()->update($payment_uuid, [
    'charge_on' => (new \Datetime('tomorrow'))->format('Y-m-d'),
    'amount'    => 11.99
]);
```

However, also the status of the planned payment might be changed – from `none` to `cancelled` or the other way. Just make sure that the `charge_on` attribute is set to correct value or explicitly set it, when changing the status.

### Refunding transactions

With Poštová banka’s iTerminal service you might once request a refund on executed transaction in part, or in full. In case of Tatra banka's CardPay service you might request as many refunds as you'd like until sum of all prior refunds reaches the amount of the original transaction.

The `reason` is more informative and should be one of either `requested_by_customer`, `fraudelent`, `duplicate` or `unknown` (default). Currency has to be the same as when executing the original payment.

```php
$payment = $client->payment()->refund($payment_uuid, [
    'amount' => 11.99,
    'currency' => \Cheddar\Currencies::EUR,
    'reason' => 'requested_by_customer'
]);
```

## Contributing

1. Check for open issues or open a new issue for a feature request or a bug.
2. Fork the repository and make your changes to the master branch (or branch off of it).
3. Send a pull request.


## TODO

* thorough tests of functionality
* ability to use your own HTTP client


## Development

Install dependencies as mentioned above, then run the test suite:

```bash
./vendor/bin/phpunit
```

Or to run an individual test file:

```bash
./vendor/bin/phpunit tests/CurlTransportTest.php
```

---

&copy; 2019 BACKBONE, s.r.o.
