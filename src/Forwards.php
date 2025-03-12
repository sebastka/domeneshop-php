<?php
namespace Sebastka\Domeneshop;

class Forwards
{
    private Client $client;
    private Domain $domain;

    /*
    * Constructor
    * @param Client $client
    * @param Domain $domain
    */
    public function __construct(Client &$client, Domain $domain)
    {
        $this->client = $client;
        $this->domain = $domain;
    }

    /*
     * Fetches all forwards and returns them, optionally filtered
     * @param array $filter (optional) Filter forwards
     * @return array
     */
    public function get(array $filter = []): array
    {
        $allForwards = $this->getAll();

        if (empty($filter))
            return $allForwards;

        // Filter forwards
        $filtered = [];
        foreach ($allForwards as $forward) {
            $match = true;
            foreach ($filter as $key => $value) {
                if (!$forward->testValue($key, $value)) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                $filtered[] = $forward;
            }
        }

        return $filtered;
    }

    /*
     * Adds a new forward
     * @param Forward $forward
     * @return void
     */
    public function add(Forward &$forward): void
    {
        $params = $forward->toArray();

        $this->client->request(
            'POST',
            '/domains/' . $this->domain->getId() . '/forwards',
            $params
        );
    }

    /*
     * Updates a forward
     * @param Forward $forward
     * @return void
     */
    public function update(Forward &$forward): void
    {
        $params = $forward->toArray();

        $this->client->request(
            'PUT',
            '/domains/' . $this->domain->getId() . '/forwards/' . $params['host'],
            $params
        );
    }

    /*
     * Deletes a forward
     * @param Forward $forward
     * @return void
     */
    public function delete(Forward &$forward): void
    {
        $params = $forward->toArray();

        $this->client->request(
            'DELETE',
            '/domains/' . $this->domain->getId() . '/forwards/' . $params['host']
        );
    }

    /*
     * Fetches all forwards and returns them
     * @return void
     */
    private function getAll(): array
    {
        $response = $this->client->request(
            'GET',
            '/domains/' . $this->domain->getId() . '/forwards'
        );

        $allForwards = [];
        foreach ($response as $forward) {
            $allForwards[] = new Forward(
                $forward['host'],
                $forward['frame'],
                $forward['url']
            );
        }

        return $allForwards;
    }

    /*
     * Same as get(), but returns Forward objects as arrays
     * @param array $filter (optional) Filter forwards
     * @return array
     */
    public function getArray(array $filter = []): array
    {
        $forwards = $this->get($filter);
        return array_map(function ($forward) { return $forward->toArray(); }, $forwards);
    }
}

?>
