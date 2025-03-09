<?php
namespace Sebastka\Domeneshop;

class Record
{
    private ?int $id;
    private string $host;
    private int $ttl;
    private string $type;
    private string $data;

    private ?int $priority;
    private ?int $weight;
    private ?int $port;
    private ?int $usage;
    private ?int $selector;
    private ?int $dtype;
    private ?int $tag;
    private ?int $alg;
    private ?string $digest;
    private ?int $flags;

    /**
     * Constructor
     * @param int|null $id
     * @param string $host
     * @param int $ttl
     * @param string $type
     * @param string $data
     */
    public function __construct(?int $id, string $host, int $ttl, string $type, string $data, array $otherFields = [])
    {
        $this->id = $id;
        $this->host = $host;
        $this->type = $type;

        $this->set('ttl', $ttl);
        $this->set('data', $data);

        // Set additional fields (ex: MX => priority)
        foreach (Client::$typeRequirements[$this->type] as $requiredFields) {
            if (!array_key_exists($requiredFields, $otherFields)) {
                throw new \Exception('Missing required field "' . $requiredFields . '"');
            }

            $this->set($requiredFields, $otherFields[$requiredFields]);
            unset($otherFields[$requiredFields]);
        }

        // Check for unknown/erroneous fields
        if (!empty($otherFields)) {
            throw new \Exception('Unknown fields for type "' . $this->type . '": ' . implode(', ', array_keys($otherFields)));
        }
    }

    /**
     * Returns the record data as an array
     * @return array
     */
    public function toArray(): array
    {
        $fields = [
            'id' => $this->id,
            'host' => $this->host,
            'ttl' => $this->ttl,
            'type' => $this->type,
            'data' => $this->data
        ];

        // Add additional fields
        foreach (Client::$typeRequirements[$this->type] as $requiredFields) {
            $fields[$requiredFields] = $this->$requiredFields;
        }

        return $fields;
    }

    /**
     * Returns the record ID
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Sets the record ID
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        if ($this->id !== NULL)
            throw new \Exception('Cannot set ID for existing record');

        $this->id = $id;
    }

    /**
     * Sets a record property and validates the value
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        // Check if property exists
        if (!property_exists($this, $key)) {
            throw new \Exception('Property "' . $key . '" does not exist');
        }

        // Check if property is allowed for this record type
        if (!in_array($key, ['ttl', 'data']) && !in_array($key, Client::$typeRequirements[$this->type]))
            throw new \Exception('Property "' . $key . '" is not allowed for type "' . $this->type . '"');

        // Convert value to integer if required
        if (in_array($key, ['ttl', 'priority', 'weight', 'port', 'usage', 'selector', 'dtype', 'tag', 'alg', 'flags'])) {
            if (!is_numeric($value))
                throw new \Exception('Property "' . $key . '" must be an integer. Given value: ' . $value);
            $value = intval($value);
        }

        // Validate value
        switch ($key) {
            case 'ttl':
                if (!(0 < $value && $value <= 2147483647))
                    throw new \Exception('Property "' . $key . '" must be an integer between 1 and 2147483647. Given value: ' . $value);
                break;
            case 'data':
                if (!is_string($value))
                    throw new \Exception('Property "' . $key . '" must be a string. Given value: ' . $value);
                break;
            case 'flags':
                if (!(0 <= $value && $value <= 255))
                    throw new \Exception('Property "' . $key . '" must be an integer between 0 and 255. Given value: ' . $value);
                break;
            case 'tag':
                if ($this->type === 'caa' && !(0 <= $value && $value <= 2))
                    throw new \Exception('Property "' . $key . '" must be an integer between 0 and 2. Given value: ' . $value);
                elseif ($this->type === 'ds' && !(0 <= $value && $value <= 2147483647))
                    throw new \Exception('Property "' . $key . '" must be an integer between 0 and 2147483647. Given value: ' . $value);
                break;
            case 'alg':
                if (!(in_array($value, [1, 2, 3, 4, 5, 252])))
                    throw new \Exception('Property "' . $key . '" must be an integer between 1, 2, 3, 4, 5 or 252. Given value: ' . $value);
                break;
            case 'digest':
                if (!is_string($value))
                    throw new \Exception('Property "' . $key . '" must be a string. Given value: ' . $value);
                break;
            case 'priority':
                if (!(0 <= $value && $value <= 65535))
                    throw new \Exception('Property "' . $key . '" must be an integer between 0 and 65535. Given value: ' . $value);
                break;
            case 'weight':
                if (!(0 <= $value && $value <= 65535))
                    throw new \Exception('Property "' . $key . '" must be an integer between 0 and 65535. Given value: ' . $value);
                break;
            case 'port':
                if (!(0 < $value && $value <= 65535))
                    throw new \Exception('Property "' . $key . '" must be an integer between 0 and 65535. Given value: ' . $value);
                break;
            case 'usage':
                if (!(0 <= $value && $value <= 3))
                    throw new \Exception('Property "' . $key . '" must be an integer between 0 and 3. Given value: ' . $value);
                break;
            case 'selector':
                if (!(0 <= $value && $value <= 1))
                    throw new \Exception('Property "' . $key . '" must be an integer between 0 and 1. Given value: ' . $value);
                break;
            case 'dtype':
                if (!(0 <= $value && $value <= 2))
                    throw new \Exception('Property "' . $key . '" must be an integer between 0 and 2. Given value: ' . $value);
                break;
            default:
                throw new \Exception('Unexpected property "' . $key . '"');
        }

        $this->$key = $value;
    }

    /**
     * Tests if a record property has a certain value
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
