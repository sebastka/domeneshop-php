<?php
namespace Sebastka\Domeneshop;

enum InvoiceStatus: string {
    case PAID = 'paid';
    case UNPAID = 'unpaid';
    case SETTLED = 'settled';
}

class Invoices
{
    private Client $client;

    /*
     * Constructor
     * @param Client $client
     */
    public function __construct(Client &$client)
    {
        $this->client = $client;
    }

    /*
     * Fetches all invoices and returns them, optionally filtered
     * @param array $filter (optional) Filter invoices
     * @return array
     */
    public function get(array $filter = []): array
    {
        $allInvoices = $this->getAll();

        if (empty($filter))
            return $allInvoices;

        // Filter invoices
        $filtered = [];
        foreach ($allInvoices as $invoice) {
            $match = true;
            foreach ($filter as $key => $value) {
                if (!$invoice->testValue($key, $value)) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                $filtered[] = $invoice;
            }
        }

        return $filtered;
    }

    /*
     * Fetches all invoices and returns them
     * @return void
     */
    private function getAll(): array
    {
        $response = $this->client->request('GET', '/invoices');

        $allInvoices = [];
        foreach ($response as $invoice) {
            $due_date = (!empty($invoice['due_date'])) ? new \DateTime($invoice['due_date']) : NULL;
            $issued_date = (!empty($invoice['issued_date'])) ? new \DateTime($invoice['issued_date']) : NULL;
            $paid_date = (!empty($invoice['paid_date'])) ? new \DateTime($invoice['paid_date']) : NULL;

            $allInvoices[] = new Invoice(
                $invoice['id'],
                $invoice['type'],
                $invoice['amount'],
                $invoice['currency'],
                $due_date,
                $issued_date,
                $paid_date,
                InvoiceStatus::from($invoice['status']),
                $invoice['url'],
            );
        }

        return $allInvoices;
    }

    /*
     * Same as get(), but returns Invoice objects as arrays
     * @param array $filter (optional) Filter invoices
     * @return array
     */
    public function getArray(array $filter = []): array
    {
        $invoices = $this->get($filter);
        return array_map(function ($invoice) { return $invoice->toArray(); }, $invoices);
    }
}

?>
