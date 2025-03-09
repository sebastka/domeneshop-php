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
    private ?string $tag;
    private ?string $alg;
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
        $this->data = $data;
        $this->ttl = $ttl;
        $this->type = $type;

        // Set additional fields (ex: MX => priority)
        foreach (Client::$typeRequirements[$this->type] as $requiredFields) {
            if (!array_key_exists($requiredFields, $otherFields)) {
                throw new \Exception('Missing required field "' . $requiredFields . '"');
            }

            $this->$requiredFields = $otherFields[$requiredFields];
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
     * Sets a record property
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        if (!property_exists($this, $key)) {
            throw new \Exception('Property "' . $key . '" does not exist');
        }

        if (!in_array($key, ['id', 'ttl', 'data']) && !in_array($key, Client::$typeRequirements[$this->type]))
            throw new \Exception('Property "' . $key . '" is not allowed for type "' . $this->type . '"');

        // Do some validation
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
