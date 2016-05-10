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
    public function getGroup($group)
    {
        $body = [
            'group_id' => $group,
            'fields' => 'members_count,screen_name',
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Ex\FloodException
     * @throws Ex\Exception
     * @throws Ex\BadResponseContentException
     * @throws Ex\AccessException
     * @throws Ex\ConnectException
     */
    public function getClients($accountId, $accessToken)
    {
        $body = [
            'account_id' => $accountId,
            'access_token' => $accessToken,
        ];

        return $this->call('ads.getClients', $body, []);
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
    public function getCampaigns($accountId, $accessToken, $clientId, $includeDeleted, array $campaignIds = null)
    {
        $campaignIdsStr = $this->jsonConverter->encode($campaignIds);
        $body = [
            'account_id' => $accountId,
            'access_token' => $accessToken,
            'client_id' => $clientId,
            'include_deleted' => $includeDeleted,
            'campaign_ids' => $campaignIdsStr,
        ];

        return $this->call('ads.getCampaigns', $body, []);
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
    public function getAds($accountId, $accessToken, $clientId, $includeDeleted, array $campaignIds = null, array $adIds = null, $limit, $offset)
    {
        $campaignIdsStr = $this->jsonConverter->encode($campaignIds);
        $adIdsStr = $this->jsonConverter->encode($adIds);
        $body = [
            'account_id' => $accountId,
            'access_token' => $accessToken,
            'client_id' => $clientId,
            'include_deleted' => $includeDeleted,
            'campaign_ids' => $campaignIdsStr,
            'ad_ids' => $adIdsStr,
            'limit' => $limit,
            'offset' => $offset,
        ];

        return $this->call('ads.getAds', $body, []);
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
    public function getAdsLayout($accountId, $accessToken, $clientId, $includeDeleted, array $campaignIds = null, array $adIds = null, $limit, $offset)
    {
        $campaignIdsStr = $this->jsonConverter->encode($campaignIds);
        $adIdsStr = $this->jsonConverter->encode($adIds);
        $body = [
            'account_id' => $accountId,
            'access_token' => $accessToken,
            'client_id' => $clientId,
            'include_deleted' => $includeDeleted,
            'campaign_ids' => $campaignIdsStr,
            'ad_ids' => $adIdsStr,
            'limit' => $limit,
            'offset' => $offset,
        ];

        return $this->call('ads.getAdsLayout', $body, []);
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
    public function getAdsTargeting($accountId, $accessToken, $clientId, $includeDeleted, array $campaignIds = null, array $adIds = null, $limit, $offset)
    {
        $campaignIdsStr = $this->jsonConverter->encode($campaignIds);
        $adIdsStr = $this->jsonConverter->encode($adIds);
        $body = [
            'account_id' => $accountId,
            'access_token' => $accessToken,
            'client_id' => $clientId,
            'include_deleted' => $includeDeleted,
            'campaign_ids' => $campaignIdsStr,
            'ad_ids' => $adIdsStr,
            'limit' => $limit,
            'offset' => $offset,
        ];

        return $this->call('ads.getAdsTargeting', $body, []);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Ex\FloodException
     * @throws Ex\Exception
     * @throws Ex\BadResponseContentException
     * @throws Ex\AccessException
     * @throws Ex\ConnectException
     */
    public function getStatistics($accountId, $accessToken, $idsType, array $ids = null, $period, $dateFrom, $dateTo)
    {
        $idsStr = implode(',', $ids);
        $body = [
            'account_id' => $accountId,
            'access_token' => $accessToken,
            'ids_type' => $idsType,
            'ids' => $idsStr,
            'period' => $period,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ];

        return $this->call('ads.getStatistics', $body, []);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Ex\FloodException
     * @throws Ex\Exception
     * @throws Ex\BadResponseContentException
     * @throws Ex\AccessException
     * @throws Ex\ConnectException
     */
    public function getDemographics($accountId, $accessToken, $idsType, array $ids = null, $period, $dateFrom, $dateTo)
    {
        $idsStr = implode(',', $ids);
        $body = [
            'account_id' => $accountId,
            'access_token' => $accessToken,
            'ids_type' => $idsType,
            'ids' => $idsStr,
            'period' => $period,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ];

        return $this->call('ads.getDemographics', $body, []);
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
    public function getTargetingStats($accountId, $accessToken, array $criteria = null, $adId, $adFormat, $adPlatform, $linkUrl, $linkDomain)
    {
        $criteriaStr = $this->jsonConverter->encode($criteria);
        $body = [
            'account_id' => $accountId,
            'access_token' => $accessToken,
            'criteria' => $criteriaStr,
            'ad_id' => $adId,
            'ad_format' => $adFormat,
            'ad_platform' => $adPlatform,
            'link_url' => $linkUrl,
            'link_domain' => $linkDomain,
        ];

        return $this->call('ads.getTargetingStats', $body, []);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Ex\FloodException
     * @throws Ex\Exception
     * @throws Ex\BadResponseContentException
     * @throws Ex\AccessException
     * @throws Ex\ConnectException
     */
    public function getCategories($accessToken, $lang = 'ru')
    {
        $body = [
            'access_token' => $accessToken,
            'lang' => $lang,
        ];

        return $this->call('ads.getCategories', $body, []);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Ex\FloodException
     * @throws Ex\Exception
     * @throws Ex\BadResponseContentException
     * @throws Ex\AccessException
     * @throws Ex\ConnectException
     */
    public function getCountries($needAll, $code, $offset, $count, $lang = 'ru')
    {
        $body = [
            'need_all' => $needAll,
            'code' => $code,
            'offset' => $offset,
            'count' => $count,
            'lang' => $lang,
        ];

        return $this->call('database.getCountries', $body, []);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Ex\FloodException
     * @throws Ex\Exception
     * @throws Ex\BadResponseContentException
     * @throws Ex\AccessException
     * @throws Ex\ConnectException
     */
    public function getCities($countryId, $regionId, $q, $needAll, $offset, $count, $lang = 'ru')
    {
        $body = [
            'country_id' => $countryId,
            'region_id' => $regionId,
            'q' => $q,
            'need_all' => $needAll,
            'offset' => $offset,
            'count' => $count,
            'lang' => $lang,
        ];

        return $this->call('database.getCities', $body, []);
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
        $errorMessage = array_key_exists('error_msg', $resp) ? $resp['error_msg'] : '';

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
