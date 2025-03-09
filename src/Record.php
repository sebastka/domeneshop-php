<?php
namespace Sebastka\Domeneshop;

class Record
{
    private int $id;
    private string $host;
    private int $ttl;
    private ValidTypes $type;
    private string $data;

    public function __construct(int $id, string $host, int $ttl, ValidTypes $type, string $data)
    {
        $this->id = $id;
        $this->host = $host;
        $this->ttl = $ttl;
        $this->type = $type;
        $this->data = $data;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'host' => $this->host,
            'ttl' => $this->ttl,
            'type' => $this->type->value,
            'data' => $this->data
        ];
    }

    public function testValue(string $key, mixed $value): bool
    {
        if (!property_exists($this, $key)) {
            throw new \Exception('Property "' . $key . '" does not exist');
        }

        return $this->$key === $value;
    }
}

?>
