<?php declare(strict_types=1);

namespace FreshMail\ApiV2;

use Exception;
use FreshMail\ApiV2\Factory\MonologFactory;
use GuzzleHttp6\Exception\ConnectException;
use GuzzleHttp6\RequestOptions;
use Psr\Log\LoggerInterface;

/**
 *  Class to make proper request (with authorization) to FreshMail Rest API V2
 *
 * @author Tadeusz Kania, Piotr Suszalski, Grzegorz Gorczyca, Piotr Leżoń
 * @since  2012-06-14
 */
class Client
{
    const HOST = 'api.freshmail.com';
    const SCHEME = 'https';
    const PREFIX = 'rest';
    const VERSION = 'v2';

    const CLIENT_VERSION = '3.0';

    /**
     * Bearer Token for authorization
     * @var string
     */
    private $bearerToken;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \GuzzleHttp6\Client
     */
    private $guzzle;

    /**
     * Client constructor.
     * @param string $bearerToken
     */
    public function __construct($bearerToken = '')
    {
        $this->bearerToken = $bearerToken;
        $this->logger = MonologFactory::createInstance();
        $this->guzzle = new \GuzzleHttp6\Client();
    }

    /**
     * @param $uri
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function doRequest(string $uri, array $params = [], bool $getRaw = false)
    {
        try {
            $method = ($params) ? 'POST' : 'GET';

            $response = $this->guzzle->request($method, $uri, $this->getRequestOptions($params));
            $rawResponse = $response->getBody()->getContents();
            if ($getRaw) {
                return $rawResponse;
            }

            $jsonResponse = json_decode($rawResponse, true);

            if (!$jsonResponse) {
                throw new ServerException(sprintf('Unable to parse response from server, raw response: %s', $rawResponse));
            }

            return $jsonResponse;
        } catch (\GuzzleHttp6\Exception\ClientException $exception) {
            if ($exception->getCode() == 401) {
                throw new UnauthorizedException('Request unauthorized');
            }

            throw new ClientException(sprintf('Connection error, error message: ' . $exception->getMessage()));
        } catch (ConnectException $exception) {
            throw new ConnectionException(sprintf('Connection error, error message: ' . $exception->getMessage()));
        }
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \GuzzleHttp6\Client $guzzle
     */
    public function setGuzzleHttpClient(\GuzzleHttp6\Client $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * @return array
     */
    private function getRequestOptions(array $requestData): array
    {
        return [
            'base_uri' => sprintf('%s://%s/%s/', self::SCHEME, self::HOST, self::PREFIX),
            RequestOptions::BODY => json_encode($requestData),
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->bearerToken,
                'User-Agent' => $this->createUserAgent()
            ]
        ];
    }

    /**
     * @return string
     */
    private function createUserAgent(): string
    {
        return
            sprintf(
                'freshmail/php-api-v2-client:%s;guzzle:%s;php:%s;interface:%s',
                self::VERSION,
                self::CLIENT_VERSION,
                PHP_VERSION,
                php_sapi_name()
            );
    }
}
