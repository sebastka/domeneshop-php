<?php
namespace Sebastka\Domeneshop;

class Invoice
{
    private int $id;
    private string $type;
    private int $amount;
    private string $currency;
    private ?\DateTime $due_date;
    private \DateTime $issued_date;
    private ?\DateTime $paid_date;
    private InvoiceStatus $status;
    private string $url;

    /**
     * Constructor
     * @param Client $client
     * @param int $id
     * @param string $type
     * @param int $amount
     * @param string $currency
     * @param \DateTime $due_date
     * @param \DateTime $issued_date
     * @param \DateTime|null $paid_date
     * @param InvoiceStatus $status
     * @param string $url
     */
    public function __construct(int $id, string $type, int $amount, string $currency, ?\DateTime $due_date, \DateTime $issued_date, ?\DateTime $paid_date, InvoiceStatus $status, string $url)
    {
        $this->id = $id;
        $this->type = $type;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->due_date = $due_date;
        $this->issued_date = $issued_date;
        $this->paid_date = $paid_date;
        $this->status = $status;
        $this->url = $url;
    }

    public function getPdfUrl(): string
    {
        $params = [];
        parse_str(parse_url($this->url, PHP_URL_QUERY), $params);
        return 'https://domene.shop/invoice?invoicenr=' . $params['nr'] . '&code=' . $params['code'] . '&lang=no&format=pdf';
    }

    /**
     * Returns the invoice data as an array
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'due_date' => ($this->due_date) ? $this->due_date->format('Y-m-d') : NULL,
            'issued_date' => ($this->issued_date) ?  $this->issued_date->format('Y-m-d') : NULL,
            'paid_date' => ($this->paid_date) ? $this->paid_date->format('Y-m-d') : NULL,
            'status' => $this->status->value,
            'url' => $this->url
        ];
    }

    /**
     * Tests if a invoice property has a certain value
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function testValue(string $key, mixed $value): bool
    {
        if (!property_exists($this, $key)) {
            throw new \Exception('Property "' . $key . '" does not exist');
        }

        return $this->$key === $value;
    }
}

?>
