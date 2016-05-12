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
     * @return RequestInterface|null
     */
    public function getLastRequest();

    /**
     * @return ResponseInterface|null
     */
    public function getLastResponse();
}