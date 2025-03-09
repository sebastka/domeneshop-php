# domeneshop-php

PHP library for working with the Domeneshop API.

## Installation

`composer require sebastka/domeneshop-php`

## Credentials

Use of this library requires Domeneshop API credentials.

See the [Domeneshop API documentation](https://api.domeneshop.no/docs/) for more information.

## Examples

Listing DNS records for the first domain in your accout:

```php
<?php

require_once 'vendor/autoload.php';

$ds = new \Sebastka\Domeneshop\Client('YOUR_API_TOKEN', 'YOUR_API_SECRET_KEY');
$domain = $ds->domains->get()[0];
$records = $domain->records->get();

foreach ($records as $record)
    print_r($record->toArray());

?>
```

## To do

Implement:
- DNS: Add, Update, Delete
- DDNS: *
- HTTPS Forwards: *
- Invoices: *
