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

    /*
     * Constructor
     * @param Client $client
     */
    public function __construct(Client &$client)
    {
        $this->client = $client;
    }

    /*
     * Fetches all domains and returns them, optionally filtered
     * @param array $filter (optional) Filter domains
     * @return array
     */
    public function get(array $filter = []): array
    {
        $allDomains = $this->getAll();

        if (empty($filter))
            return $allDomains;

        // Filter domains
        $filtered = [];
        foreach ($allDomains as $domain) {
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
     * Fetches all domains and returns them
     * @return void
     */
    private function getAll(): array
    {
        $response = $this->client->request('GET', '/domains');

        $allDomains = [];
        foreach ($response as $domain) {
            $allDomains[] = new Domain(
                $this->client,
                $domain['id'],
                $domain['domain'],
                new \DateTime($domain['expiry_date']),
                (!empty($domain['registered_date'])) ? new \DateTime($domain['registered_date']) : NULL,
                (!empty($domain['transferred_date'])) ? new \DateTime($domain['transferred_date']) : NULL,
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

        return $allDomains;
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
