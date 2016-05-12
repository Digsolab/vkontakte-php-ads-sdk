<?php

namespace DSL\Client\Vkontakte;

use DSL\Converter\ConversionException;
use DSL\Converter\JsonConverter;
use DSL\Client\Response as ClientResponse;
use DSL\Client\Vkontakte\Exception as Ex;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class VkontakteClient implements VkontakteClientInterface
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
    /** @var RequestInterface */
    protected $lastRequest;
    /** @var ResponseInterface */
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
     * {@inheritdoc}
     *
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
     * {@inheritdoc}
     *
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
     * {@inheritdoc}
     *
     * @throws Ex\FloodException
     * @throws Ex\Exception
     * @throws Ex\BadResponseContentException
     * @throws Ex\AccessException
     * @throws Ex\ConnectException
     * @throws GuzzleException
     */
    public function getGroup($group, array $fields = [])
    {
        if (0 === count($fields)) {
            $fields = ['members_count', 'screen_name'];
        }
        $body = [
            'group_id' => $group,
            'fields' => implode(',', $fields),
        ];

        return $this->call('groups.getById', $body, []);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \DSL\Converter\ConversionException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Ex\FloodException
     * @throws Ex\Exception
     * @throws Ex\BadResponseContentException
     * @throws Ex\AccessException
     * @throws Ex\ConnectException
     */
    public function getCoverage($accountId, $accessToken, array $settings, $linkDomain)
    {
        $settingsStr = $this->jsonConverter->encode($settings);
        $body = [
            'account_id' => $accountId,
            'access_token' => $accessToken,
            'criteria' => $settingsStr,
            'link_domain' => $linkDomain,
            'link_url' => '1',
        ];

        return $this->call('ads.getTargetingStats', $body, []);
    }

    /**
     * {@inheritdoc}
     *
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
     * {@inheritdoc}
     *
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
        $this->lastResponse = $this->lastRequest = null;
        try {
            $request = new Request('POST', $uri, $headers, http_build_query($body));
            $this->lastRequest = $request;
            $response = $this->transport->send($request);
            $this->lastResponse = $response;
        } catch (TransferException $exception) {
            throw new Ex\ConnectException($exception->getMessage());
        } catch (\Exception $exception) {
            throw new Ex\Exception($exception->getMessage());
        }

        return $this->parseResponse($response);
    }

    /**
     * {@inheritdoc}
     *
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

        if (array_key_exists('error', $body)) {
            list($errorCode, $errorMessage) = $this->parseError($body['error']);

            if (in_array($errorCode, self::$floodErrors, true)) {
                throw new Ex\FloodException($errorMessage, $errorCode);
            } else if (in_array($errorCode, self::$accessErrors, true)) {
                throw new Ex\AccessException($errorMessage, $errorCode);
            } else if ( ! in_array($errorCode, self::$partialErrors, true)) {
                throw new Ex\Exception($errorMessage, $errorCode);
            }
        } else if (array_key_exists('response', $body)) {
            $clientResponses = [];
            if ( array_key_exists('error_code', $body['response']) || array_key_exists('error_desc', $body['response'])) {
                $body['response'] = [$body['response']];
            }
            foreach ($body['response'] as $resp) {
                list($errorCode, $errorMessage) = $this->parseError($resp);
                $clientResponses[] = new ClientResponse($statusCode, $resp, $errorCode, $errorMessage);
            }

            return $clientResponses;
        } else {
            throw new Ex\Exception('Invalid response', self::ERROR_UNKNOWN);
        }

        throw new Ex\BadResponseContentException('Vk error - unknown response', self::ERROR_UNKNOWN);
    }

    /**
     * @param $resp
     *
     * @return array
     */
    private function parseError(array $resp)
    {
        $errorCode = array_key_exists('error_code', $resp) ? $resp['error_code'] : 0;
        $errorMessage = '';

        if (array_key_exists('error_desc', $resp)) {
            $errorMessage = $resp['error_desc'];
        }
        if (array_key_exists('error_msg', $resp)) {
            $errorMessage = $resp['error_msg'];
        }

        return [$errorCode, $errorMessage];
    }

    /**
     * @return RequestInterface
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * @return ResponseInterface
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }
}
