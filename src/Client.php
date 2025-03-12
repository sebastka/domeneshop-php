<?php
namespace Sebastka\Domeneshop;

class Client
{
    public static string $DEFAULT_API_BASE = 'https://api.domeneshop.no/v0';
    public static string $DEFAULT_USER_AGENT = 'sebastka/domeneshop';
    public static array $recordTypes = [
        'A',
        'AAAA',
        'ANAME',
        'CAA',
        'CNAME',
        'DS',
        'HTTPS',
        'MX',
        'NS',
        'SRV',
        'SVCB',
        'TLSA',
        'TXT'
    ];
    public static array $typeRequirements = [
        'A' => [],
        'AAAA' => [],
        'ANAME' => [],
        'CAA' => ['flags', 'tag'],
        'CNAME' => [],
        'DS' => ['tag', 'alg', 'digest'],
        'HTTPS' => ['priority'],
        'MX' => ['priority'],
        'NS' => [],
        'SRV' => ['priority', 'weight', 'port'],
        'SVCB' => ['priority'],
        'TLSA' => ['usage', 'selector', 'dtype'],
        'TXT' => []
    ];

    private \CurlHandle $curl;
    public Domains $domains;
    public Invoices $invoices;

    /**
     * Constructor
     * @param string $token
     * @param string $secret
     */
    public function __construct(string $token, string $secret)
    {
        $this->initClient($token, $secret);
        $this->domains = new Domains($this);
        $this->invoices = new Invoices($this);
    }

    /**
     * Initializes the cURL client
     * @param string $token
     * @param string $secret
     * @return void
     */
    private function initClient(string $token, string $secret): void
    {
        $this->curl = curl_init();

        // HTTP Basic auth
        curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($this->curl, CURLOPT_USERPWD, $token . ':' . $secret);

        curl_setopt($this->curl, CURLOPT_USERAGENT, self::$DEFAULT_USER_AGENT);

        // Default options
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
    }

    /**
     * Run a request
     * @param string $method
     * @param string $path
     * @param array $data
     * @return array
     */
    public function request(string $method, string $path, array $data = []): ?array
    {
        curl_setopt($this->curl, CURLOPT_URL, self::$DEFAULT_API_BASE . $path);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);

        switch ($method) {
            case 'GET':
                curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
                break;
            case 'POST':
                curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'PUT':
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($data));
                break;
        }

        $response = curl_exec($this->curl);
        $info = curl_getinfo($this->curl);

        if ($response === false) {
            throw new \Exception(curl_error($this->curl));
        }

        $response = json_decode($response, true);

        switch ($info['http_code']) {
            case 200:
                break;
            case 201:
                break;
            case 204:
                break;
            default:
                throw new \Exception('HTTP ' . $info['http_code'] . ': ' . $response['code']);
        }

        return $response;
    }
}

?>
