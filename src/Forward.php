<?php
namespace Sebastka\Domeneshop;

class Forward
{
    private string $host;
    private bool $frame;
    private string $url;

    /**
     * Constructor
     * @param string $host
     * @param bool $frame
     * @param string $url
     */
    public function __construct(string $host, bool $frame, string $url)
    {
        $this->set('host', $host);
        $this->set('frame', $frame);
        $this->set('url', $url);
    }

    /**
     * Returns the forward data as an array
     * @return array
     */
    public function toArray(): array
    {
        return [
            'host' => $this->host,
            'frame' => $this->frame,
            'url' => $this->url
        ];
    }

    /**
     * Sets a forward property and validates the value
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

        $this->$key = $value;
    }

    /**
     * Tests if a forward property has a certain value
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
