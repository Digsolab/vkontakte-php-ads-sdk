<?php
namespace DSL\Client\Vkontakte;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use DSL\Client\Response as ClientResponse;

interface VkontakteClientInterface
{
    /**
     * @param int $accountId
     * @param string $accessToken
     * @param int $clientId
     * @param double $limit
     *
     * @return ClientResponse[]
     */
    public function updateClientAllLimit($accountId, $accessToken, $clientId, $limit);

    /**
     * @param array $campaignIds
     * @param int $accountId
     * @param string $accessToken
     * @param string $status
     *
     * @return ClientResponse[]
     */
    public function updateCampaignsStatus(array $campaignIds, $accountId, $accessToken, $status);

    /**
     * @param string $group
     *
     * @return ClientResponse[]
     */
    public function getGroup($group);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param array $settings
     * @param string $linkDomain
     *
     * @return ClientResponse[]
     */
    public function getCoverage($accountId, $accessToken, array $settings, $linkDomain);

    /**
     * @param int $accountId
     * @param string $accessToken
     *
     * @return ClientResponse[]
     */
    public function getClients($accountId, $accessToken);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param int $clientId
     * @param int $includeDeleted
     * @param int[] $campaignIds
     *
     * @return ClientResponse[]
     */
    public function getCampaigns($accountId, $accessToken, $clientId, $includeDeleted, array $campaignIds);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param int $clientId
     * @param int $includeDeleted
     * @param int[] $campaignIds
     * @param int[] $adIds
     * @param int $limit
     * @param int $offset
     *
     * @return ClientResponse[]
     */
    public function getAds($accountId, $accessToken, $clientId, $includeDeleted, array $campaignIds, array $adIds, $limit, $offset);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param int $clientId
     * @param int $includeDeleted
     * @param int[] $campaignIds
     * @param int[] $adIds
     * @param int $limit
     * @param int $offset
     *
     * @return ClientResponse[]
     */
    public function getAdsLayout($accountId, $accessToken, $clientId, $includeDeleted, array $campaignIds, array $adIds, $limit, $offset);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param int $clientId
     * @param int $includeDeleted
     * @param int[] $campaignIds
     * @param int[] $adIds
     * @param int $limit
     * @param int $offset
     *
     * @return ClientResponse[]
     */
    public function getAdsTargeting($accountId, $accessToken, $clientId, $includeDeleted, array $campaignIds, array $adIds, $limit, $offset);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param string $idsType
     * @param int[]  $ids
     * @param string $period
     * @param string $dateFrom
     * @param string $dateTo
     *
     * @return ClientResponse[]
     */
    public function getStatistics($accountId, $accessToken, $idsType, array $ids, $period, $dateFrom, $dateTo);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param string $idsType
     * @param int[]  $ids
     * @param string $period
     * @param string $dateFrom
     * @param string $dateTo
     *
     * @return ClientResponse[]
     */
    public function getDemographics($accountId, $accessToken, $idsType, array $ids, $period, $dateFrom, $dateTo);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param array $criteria
     * @param int $adId
     * @param int $adFormat
     * @param string $adPlatform
     * @param string $linkUrl
     * @param string $linkDomain
     *
     * @return ClientResponse[]
     */
    public function getTargetingStats($accountId, $accessToken, array $criteria, $adId, $adFormat, $adPlatform, $linkUrl, $linkDomain);

    /**
     * @return RequestInterface|null
     */
    public function getLastRequest();

    /**
     * @return ResponseInterface|null
     */
    public function getLastResponse();
}