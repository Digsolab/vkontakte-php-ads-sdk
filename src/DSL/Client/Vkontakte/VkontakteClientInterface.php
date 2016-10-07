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
    public function getGroup($group, array $fields);

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
     * @param string $accessToken
     * @param string $lang
     *
     * @return ClientResponse[]
     */
    public function getCategories($accessToken, $lang);

    /**
     * @param int $needAll
     * @param string $code
     * @param int $offset
     * @param int $count
     * @param string $lang
     *
     * @return ClientResponse[]
     */
    public function getCountries($needAll, $code, $offset, $count, $lang);

    /**
     * @param int $countryId
     * @param string $q
     * @param int $offset
     * @param int $count
     * @param string $lang
     *
     * @return ClientResponse[]
     */
    public function getRegions($countryId, $q, $offset, $count, $lang);

    /**
     * @param int $countryId
     * @param int $regionId
     * @param string $q
     * @param int $needAll
     * @param int $offset
     * @param int $count
     * @param string $lang
     *
     * @return ClientResponse[]
     */
    public function getCities($countryId, $regionId, $q, $needAll, $offset, $count, $lang);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param array $clientsData
     *
     * @return ClientResponse[]
     */
    public function createClients($accountId, $accessToken, array $clientsData);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param array $clientsData
     *
     * @return ClientResponse[]
     */
    public function updateClients($accountId, $accessToken, array $clientsData);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param array $clientIds
     *
     * @return ClientResponse[]
     */
    public function deleteClients($accountId, $accessToken, array $clientIds);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param array $campaignsData
     *
     * @return ClientResponse[]
     */
    public function createCampaigns($accountId, $accessToken, array $campaignsData);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param array $campaignsData
     *
     * @return ClientResponse[]
     */
    public function updateCampaigns($accountId, $accessToken, array $campaignsData);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param array $campaignIds
     *
     * @return ClientResponse[]
     */
    public function deleteCampaigns($accountId, $accessToken, array $campaignIds);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param array $adsData
     *
     * @return ClientResponse[]
     */
    public function createAds($accountId, $accessToken, array $adsData);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param array $adsData
     *
     * @return ClientResponse[]
     */
    public function updateAds($accountId, $accessToken, array $adsData);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param array $adIds
     *
     * @return ClientResponse[]
     */
    public function deleteAds($accountId, $accessToken, array $adIds);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param string $linkType
     * @param string $linkUrl
     * @param int $campaignId
     *
     * @return ClientResponse[]
     */
    public function checkLink($accountId, $accessToken, $linkType, $linkUrl, $campaignId);

    /**
     * @param string $accessToken
     * @param int $adFormat
     *
     * @return ClientResponse[]
     */
    public function getUploadURL($accessToken, $adFormat);

    /**
     * @param int $accountId
     * @param string $accessToken
     *
     * @return ClientResponse[]
     */
    public function getFloodStats($accountId, $accessToken);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param int $adId
     *
     * @return ClientResponse[]
     */
    public function getRejectionReason($accountId, $accessToken, $adId);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param int $targetGroupId
     * @param string $name
     * @param string $domain
     * @param int $lifetime
     * @param int|null $clientId
     *
     * @return ClientResponse[]
     */
    public function updateTargetGroup($accountId, $accessToken, $targetGroupId, $name, $domain = '', $lifetime = 0, $clientId = null);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param int $targetGroupId
     * @param int|null $clientId
     *
     * @return ClientResponse[]
     */
    public function deleteTargetGroup($accountId, $accessToken, $targetGroupId, $clientId = null);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param int $extended
     * @param int|null $clientId
     *
     * @return ClientResponse[]
     */
    public function getTargetGroups($accountId, $accessToken, $extended = 0, $clientId = null);

    /**
     * @param int $accountId
     * @param string $accessToken
     * @param int $targetGroupId
     * @param string $contacts
     * @param int|null $clientId
     *
     * @return ClientResponse[]
     */
    public function importTargetContacts($accountId, $accessToken, $targetGroupId, $contacts, $clientId = null);

    /**
     * @return RequestInterface|null
     */
    public function getLastRequest();

    /**
     * @return ResponseInterface|null
     */
    public function getLastResponse();
}