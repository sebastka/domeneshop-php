<?php
namespace Sebastka\Domeneshop;

enum ValidTypes: string {
    case A = 'A';
    case AAAA = 'AAAA';
    case CNAME = 'CNAME';
    case ANAME = 'ANAME';
    case TLSA = 'TLSA';
    case MX = 'MX';
    case SRV = 'SRV';
    case DS = 'DS';
    case CAA = 'CAA';
    case NS = 'NS';
    case TXT = 'TXT';
}

enum CommonKeys: string {
    case HOST = 'host';
    case DATA = 'data';
    case TTL = 'ttl';
    case TYPE = 'type';
}

enum SpecificKeys: string {
    case PRIORITY = 'priority';
    case WEIGHT = 'weight';
    case PORT = 'port';
    case USAGE = 'usage';
    case SELECTOR = 'selector';
    case DTYPE = 'dtype';
    case TAG = 'tag';
    case ALG = 'alg';
    case DIGEST = 'digest';
    case FLAGS = 'flags';
}

class ValidKeys {
    const MX = [SpecificKeys::PRIORITY];
    const SRV = [SpecificKeys::PRIORITY, SpecificKeys::WEIGHT, SpecificKeys::PORT];
    const TLSA = [SpecificKeys::USAGE, SpecificKeys::SELECTOR, SpecificKeys::DTYPE];
    const DS = [SpecificKeys::TAG, SpecificKeys::ALG, SpecificKeys::DIGEST];
    const CAA = [SpecificKeys::FLAGS, SpecificKeys::TAG];
}

class Records
{
    private Client $client;
    private Domain $domain;
    private array $records;

    public function __construct(Client &$client, Domain $domain)
    {
        $this->client = $client;
        $this->domain = $domain;
    }

    /*
     * Get records
     * @param array $filter (optional) Filter records
     * @return array
     */
    public function get(array $filter = []): array
    {
        $this->getRecords();

        if (empty($filter))
            return $this->records;

        // Filter records
        $filtered = [];
        foreach ($this->records as $record) {
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

    private function getRecords(): void
    {
        $response = $this->client->get('/domains/' . $this->domain->getId() . '/dns');

        $this->records = [];
        foreach ($response as $record) {
            $this->records[] = new Record(
                $record['id'],
                $record['host'],
                $record['ttl'],
                ValidTypes::from($record['type']),
                $record['data']
            );
        }
    }

    /*
     * Get records as array
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
