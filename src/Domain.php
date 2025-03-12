<?php
namespace Sebastka\Domeneshop;

class Domain
{
    private int $id;
    private string $domain;
    private \DateTime $expiry_date;
    private ?\DateTime $registered_date;
    private ?\DateTime $transferred_date;
    private bool $renew;
    private string $registrant;
    private ActiveStatus $status;
    private array $nameservers;
    private bool $service_registrar;
    private bool $service_dns;
    private bool $service_email;
    private bool $service_webhotel;
    public Records $records;
    public Forwards $forwards;

    /**
     * Constructor
     * @param Client $client
     * @param int $id
     * @param string $domain
     * @param \DateTime $expiry_date
     * @param \DateTime|null $registered_date
     * @param \DateTime|null $transferred_date
     * @param bool $renew
     * @param string $registrant
     * @param ActiveStatus $status
     * @param array $nameservers
     * @param bool $service_registrar
     * @param bool $service_dns
     * @param bool $service_email
     * @param bool $service_webhotel
     */
    public function __construct(Client &$client, int $id, string $domain, \DateTime $expiry_date, ?\DateTime $registered_date, ?\DateTime $transferred_date, bool $renew, string $registrant, ActiveStatus $status, array $nameservers, bool $service_registrar, bool $service_dns, bool $service_email, bool $service_webhotel)
    {
        $this->id = $id;
        $this->domain = $domain;
        $this->expiry_date = $expiry_date;
        $this->registered_date = $registered_date;
        $this->transferred_date = $transferred_date;
        $this->renew = $renew;
        $this->registrant = $registrant;
        $this->status = $status;
        $this->nameservers = $nameservers;
        $this->service_registrar = $service_registrar;
        $this->service_dns = $service_dns;
        $this->service_email = $service_email;
        $this->service_webhotel = $service_webhotel;
        $this->records = new Records($client, $this);
        $this->forwards = new Forwards($client, $this);
    }

    /**
     * Returns the domain data as an array
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'domain' => $this->domain,
            'expiry_date' => $this->expiry_date->format('Y-m-d'),
            'registered_date' => $this->registered_date ? $this->registered_date->format('Y-m-d') : NULL,
            'transferred_date' => $this->transferred_date ? $this->transferred_date->format('Y-m-d') : NULL,
            'renew' => $this->renew,
            'registrant' => $this->registrant,
            'status' => $this->status->value,
            'nameservers' => $this->nameservers,
            'services' => [
                'registrar' => $this->service_registrar,
                'dns' => $this->service_dns,
                'email' => $this->service_email,
                'webhotel' => $this->service_webhotel
            ]
        ];
    }


    /**
     * Returns the domain ID
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Tests if a domain property has a certain value
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
