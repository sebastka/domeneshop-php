<?php
namespace Sebastka\Domeneshop;

class Records
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
     * Fetches all records and returns them, optionally filtered
     * @param array $filter (optional) Filter records
     * @return array
     */
    public function get(array $filter = []): array
    {
        $allRecords = $this->getAll();

        if (empty($filter))
            return $allRecords;

        // Filter records
        $filtered = [];
        foreach ($allRecords as $record) {
            $match = true;
            foreach ($filter as $key => $value) {
                if (!$record->testValue($key, $value)) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                $filtered[] = $record;
            }
        }

        return $filtered;
    }

    /*
     * Adds a new record
     * @param Record $record
     * @return void
     */
    public function add(Record &$record): void
    {
        $data = $record->toArray();
        $params = [
            'host' => $data['host'],
            'ttl' => $data['ttl'],
            'type' => $data['type'],
            'data' => $data['data']
        ];

        foreach (Client::$typeRequirements[$data['type']] as $requiredFields) {
            $params[$requiredFields] = $data[$requiredFields];
        }

        $id = $this->client->request(
            'POST',
            '/domains/' . $this->domain->getId() . '/dns',
            $params
        )['id'];

        $record->setID($id);
    }

    /*
     * Updates a record
     * @param Record $record
     * @return void
     */
    public function update(Record &$record): void
    {
        $data = $record->toArray();
        $params = [
            'host' => $data['host'],
            'ttl' => $data['ttl'],
            'type' => $data['type'],
            'data' => $data['data']
        ];

        foreach (Client::$typeRequirements[$data['type']] as $requiredFields) {
            $params[$requiredFields] = $data[$requiredFields];
        }

        $this->client->request(
            'PUT',
            '/domains/' . $this->domain->getId() . '/dns/' . $record->getId(),
            $params
        );
    }

    /*
     * Deletes a record
     * @param Record $record
     * @return void
     */
    public function delete(Record &$record): void
    {
        $this->client->request(
            'DELETE',
            '/domains/' . $this->domain->getId() . '/dns/' . $record->getId()
        );
        $record->delete(NULL);
    }

    /*
     * Fetches all records and returns them
     * @return void
     */
    private function getAll(): array
    {
        $response = $this->client->request(
            'GET',
            '/domains/' . $this->domain->getId() . '/dns'
        );

        $allRecords = [];
        foreach ($response as $record) {
            $otherFields = [];
            foreach (Client::$typeRequirements[$record['type']] as $requiredFields) {
                $otherFields[$requiredFields] = $record[$requiredFields];
            }
            
            $allRecords[] = new Record(
                $record['id'],
                $record['host'],
                $record['ttl'],
                $record['type'],
                $record['data'],
                $otherFields
            );
        }

        return $allRecords;
    }

    /*
     * Same as get(), but returns Record objects as arrays
     * @param array $filter (optional) Filter records
     * @return array
     */
    public function getArray(array $filter = []): array
    {
        $records = $this->get($filter);
        return array_map(function ($record) { return $record->toArray(); }, $records);
    }
}

?>
