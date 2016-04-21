<?php

namespace DSL\Client\Vkontakte;

use DSL\Converter\ConversionException;
use DSL\Converter\JsonConverter;
use DSL\Client\Response as ClientResponse;
use DSL\Client\Vkontakte\Exception as Ex;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Psr\Http\Message\ResponseInterface;

class VkontakteClient
{
    const STATUS_PARTIAL_DONE = 602;
    const STATUS_NOT_DONE = 603;
    const ERROR_INVALID_INPUT = 100;
    const ERROR_UNKNOWN = - 100;

    protected static $floodErrors = [6, 9, 601];
    protected static $accessErrors = [2, 4, 5, 101, 600];
    protected static $partialErrors = [self::STATUS_PARTIAL_DONE, self::STATUS_NOT_DONE];

    /** @var ClientInterface */
    protected $transport;
    /** @var JsonConverter */
    protected $jsonConverter;
    /** @var GuzzleRequest */
    protected $lastRequest;
    /** @var GuzzleResponse */
    protected $lastResponse;

    /**
     * @param ClientInterface $transport
     * @param JsonConverter   $jsonConverter
     */
    public function __construct(ClientInterface $transport, JsonConverter $jsonConverter)
    {
        $this->transport = $transport;
        $this->jsonConverter = $jsonConverter;
    }

    /**
     * @param $accountId
     * @param $accessToken
     * @param $clientId
     * @param $limit
     *
     * @return ClientResponse[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Ex\FloodException
     * @throws Ex\Exception
     * @throws Ex\AccessException
     * @throws Ex\ConnectException
     * @throws Ex\BadResponseContentException
     */
    public function updateClientAllLimit($accountId, $accessToken, $clientId, $limit)
    {
        try {
            $data = $this->jsonConverter->encode([['client_id' => $clientId, 'all_limit' => $limit]]);
        } catch (ConversionException $e) {
            throw new Ex\BadResponseContentException($e->getMessage(), self::ERROR_UNKNOWN);
        }
        $body = [
            'account_id' => $accountId,
            'access_token' => $accessToken,
            'data' => $data,
        ];

        return $this->call('ads.updateClients', $body, []);
    }

    /**
     * @param array $campaignIds
     * @param int   $accountId
     * @param int   $accessToken
     * @param int   $status
     *
     * @return ClientResponse[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Ex\FloodException
     * @throws Ex\Exception
     * @throws Ex\ConnectException
     * @throws Ex\AccessException
     * @throws Ex\BadResponseContentException
     */
    public function updateCampaignsStatus(array $campaignIds, $accountId, $accessToken, $status)
    {

        $campaignStatusData = array_map(
            function ($campaignId) use ($status) {
                return [
                    'campaign_id' => $campaignId,
                    'status' => $status,
                ];
            },
            $campaignIds
        );
        try {
            $data = $this->jsonConverter->encode(
                $campaignStatusData
            );
        } catch (ConversionException $e) {
            throw new Ex\BadResponseContentException($e->getMessage(), self::ERROR_UNKNOWN);
        }
        $body = [
            'account_id' => $accountId,
            'access_token' => $accessToken,
            'data' => $data,
        ];

        return $this->call('ads.updateCampaigns', $body, []);
    }

    /**
     * @param $group
     *
     * @return ClientResponse[]
     * @throws Ex\FloodException
     * @throws Ex\Exception
     * @throws Ex\BadResponseContentException
     * @throws Ex\AccessException
     * @throws Ex\ConnectException
     *
     * @throws GuzzleException
     */
    public function getGroup($group)
    {
        $body = [
            'group_id' => $group,
            'fields' => 'members_count,screen_name',
        ];

        return $this->call('groups.getById', $body, []);
    }

    /**
     * @param int    $accountId
     * @param string $accessToken
     * @param array  $settings
     * @param string $linkDomain
     *
     * @return ClientResponse[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Ex\FloodException
     * @throws Ex\Exception
     * @throws Ex\BadResponseContentException
     * @throws Ex\AccessException
     * @throws Ex\ConnectException
     */
    public function getCoverage($accountId, $accessToken, array $settings, $linkDomain)
    {
        $body = [
            'account_id' => $accountId,
            'access_token' => $accessToken,
            'criteria' => $settings,
            'link_domain' => $linkDomain,
            'link_url' => '1',
        ];

        return $this->call('ads.getTargetingStats', $body, []);
    }

    /**
     * @param string $uri
     * @param array  $body
     * @param array  $headers
     *
     * @return ClientResponse[]
     * @throws GuzzleException
     * @throws Ex\FloodException
     * @throws Ex\Exception
     * @throws Ex\BadResponseContentException
     * @throws Ex\AccessException
     * @throws Ex\ConnectException
     */
    protected function call($uri, array $body, array $headers = [])
    {
        $headers = array_merge(
            $headers,
            [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]
        );

        return $this->doRequest($uri, $body, $headers);
    }

    /**
     * @param string $uri
     * @param string $body
     * @param array  $headers
     *
     * @return ClientResponse[]
     * @throws Ex\FloodException
     * @throws Ex\BadResponseContentException
     * @throws Ex\AccessException
     * @throws Ex\Exception
     * @throws Ex\ConnectException
     * @throws GuzzleException
     */
    protected function doRequest(
        $uri,
        $body = null,
        array $headers = []
    ) {
        try {
            $request = new GuzzleRequest('POST', $uri, $headers, http_build_query($body));
            $response = $this->transport->send($request);
            $this->lastRequest = $request;
            $this->lastResponse = $response;
        } catch (TransferException $exception) {
            throw new Ex\ConnectException($exception->getMessage());
        } catch (\Exception $exception) {
            throw new Ex\Exception($exception->getMessage());
        }

        return $this->parseResponse($response);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ClientResponse[]
     * @throws Ex\BadResponseContentException
     * @throws Ex\Exception
     * @throws Ex\AccessException
     * @throws Ex\FloodException
     */
    private function parseResponse(ResponseInterface $response)
    {
        $statusCode = $response->getStatusCode();

        try {
            $body = $this->jsonConverter->decode(
                (string) $response->getBody(),
                true,
                'Decoding error while parse VkontakteResponse'
            );
        } catch (ConversionException $exception) {
            throw new Ex\BadResponseContentException($exception->getMessage(), self::ERROR_UNKNOWN);
        }

        if (isset($body['error'])) {
            $errorCode = isset($body['error']['error_code']) ? (int) $body['error']['error_code'] : 0;
            $errorMessage = isset($body['error']['error_msg']) ? $body['error']['error_msg'] : '';

            if (in_array($errorCode, self::$floodErrors, true)) {
                throw new Ex\FloodException($errorMessage, $errorCode);
            } else if (in_array($errorCode, self::$accessErrors, true)) {
                throw new Ex\AccessException($errorMessage, $errorCode);
            } else if ( ! in_array($errorCode, self::$partialErrors, true)) {
                throw new Ex\Exception($errorMessage, $errorCode);
            }
        } else if (isset($body['response'])) {
            $clientResponses = [];
            if (is_array($body['response'])) {
                foreach ($body['response'] as $resp) {
                    $errorCode = isset($resp['error_code']) ? $resp['error_code'] : 0;
                    $errorMessage = isset($resp['error_desc']) ? $resp['error_desc'] : '';
                    $clientResponses[] = new ClientResponse($statusCode, $resp, $errorCode, $errorMessage);
                }
            } else {
                $errorCode = isset($body['response']['error_code']) ? $body['response']['error_code'] : 0;
                $errorMessage = isset($body['response']['error_desc']) ? $body['response']['error_desc'] : '';
                $clientResponses[] = new ClientResponse($statusCode, $body['response'], $errorCode, $errorMessage);
            }

            return $clientResponses;
        } else {
            throw new Ex\Exception('Invalid response', self::ERROR_UNKNOWN);
        }

        throw new Ex\BadResponseContentException('Vk error - unknown response', self::ERROR_UNKNOWN);
    }

    /**
     * @return GuzzleRequest
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * @return GuzzleResponse
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }
}
