<?php
namespace Sebastka\Domeneshop;

class Client
{
    const string DEFAULT_API_BASE = 'https://api.domeneshop.no/v0';
    const string DEFAULT_USER_AGENT = 'sebastka/domeneshop';

    private \CurlHandle $curl;
    public Domains $domains;

    public function __construct(string $token, string $secret)
    {
        $this->initClient($token, $secret);
        $this->domains = new Domains($this);
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }

    private function initClient(string $token, string $secret): void
    {
        $this->curl = curl_init();

        // HTTP Basic auth
        curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($this->curl, CURLOPT_USERPWD, $token . ':' . $secret);

        curl_setopt($this->curl, CURLOPT_USERAGENT, self::DEFAULT_USER_AGENT);

        // Default options
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: ' . self::DEFAULT_USER_AGENT
        ]);
    }

    public function get(string $path): array
    {
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_URL, self::DEFAULT_API_BASE . $path);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'GET');

        $response = curl_exec($this->curl);
        $info = curl_getinfo($this->curl);

        if ($response === false) {
            throw new \Exception(curl_error($this->curl));
        }

        $response = json_decode($response, true);

        switch ($info['http_code']) {
            case 200:
                break;
            default:
                throw new \Exception('HTTP ' . $info['http_code'] . ': ' . $response['code']);
        }

        return $response;
    }
}

?>
