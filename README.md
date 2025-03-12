# domeneshop-php

PHP library for working with the Domeneshop API.

## Installation

`composer require sebastka/domeneshop-php`

## Credentials

Use of this library requires Domeneshop API credentials.

See the [Domeneshop API documentation](https://api.domeneshop.no/docs/) for more information.

## Examples

Operations on the first domain in your accout:

```php
<?php

require_once 'vendor/autoload.php';

$ds = new \Sebastka\Domeneshop\Client('YOUR_API_TOKEN', 'YOUR_API_SECRET_KEY');
$domain = $ds->domains->get()[0];

// Delete all _acme-challenge records
$recordsToDelete = $domain->records->get(['type' => 'TXT', 'host' => '_acme-challenge']);
foreach ($recordsToDelete as $record)
    $domain->records->delete($record);

// Show all remaining records
$allRecords = $domain->records->get();
foreach ($allRecords as $record)
    print_r($record->toArray());

// Add a new record
$new_record = new \Sebastka\Domeneshop\Record(
    NULL,
    '@',
    3600,
    'MX',
    'mx.domain.tdl'
    [ 'priority' => 10 ]
);
$domain->records->add($new_record);

// Update record
$new_record->set('data', 'mx.domain.tld');
$new_record->set('priority', 20);
$domain->records->update($new_record);

?>
```

## To do

Implement:
- DDNS: *
- HTTPS Forwards: *
- Tests
