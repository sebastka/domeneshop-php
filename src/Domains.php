<?php
namespace Sebastka\Domeneshop;

enum ActiveStatus: string {
    case ACTIVE = 'active';
    case EXPIRES = 'expired';
    case DEACTIVATED = 'deactivated';
    case PENDING = 'pendingDeleteRestorable';
}

class Domains
{
    private Client $client;
    private array $domains;

    public function __construct(Client &$client)
    {
        $this->client = $client;
    }

    /*
     * Get domains
     * @param array $filter (optional) Filter domains
     * @return array
     */
    public function get(array $filter = []): array
    {
        // Initialize $this->domains
        $this->getDomains();

        // Return all domains if no filter
        if (empty($filter))
            return $this->domains;

        // Filter domains
        $filtered = [];
        foreach ($this->domains as $domain) {
            $match = true;
            foreach ($filter as $key => $value) {
                if (!$domain->testValue($key, $value)) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                $filtered[] = $domain;
            }
        }

        return $filtered;
    }

    /*
     * Get all domains
     * @return array
     */
    private function getDomains(): void
    {
        $response = $this->client->get('/domains');

        $this->domains = [];
        foreach ($response as $domain) {
            $registered_date = (!empty($domain['registered_date'])) ? new \DateTime($domain['registered_date']) : NULL;
            $transferred_date = (!empty($domain['transferred_date'])) ? new \DateTime($domain['transferred_date']) : NULL;

            $this->domains[] = new Domain(
                $this->client,
                $domain['id'],
                $domain['domain'],
                new \DateTime($domain['expiry_date']),
                $registered_date,
                $transferred_date,
                $domain['renew'],
                $domain['registrant'],
                ActiveStatus::from($domain['status']),
                $domain['nameservers'],
                $domain['services']['registrar'],
                $domain['services']['dns'],
                $domain['services']['email'],
                $domain['services']['webhotel']
            );
        }
    }

    /*
     * Same as get(), but returns Domain objects as arrays
     * @param array $filter (optional) Filter domains
     * @return array
     */
    public function getArray(array $filter = []): array
    {
        $domains = $this->get($filter);
        return array_map(function ($domain) { return $domain->toArray(); }, $domains);
    }
}

?>
