<?php


namespace DSL\Client\Tests;


use DSL\Client\Vkontakte\VkontakteClient;
use DSL\Converter\JsonConverter;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerInterface;

class VkontakteClientTest extends \PHPUnit_Framework_TestCase
{

    /** @var  VkontakteClient */
    protected $client;

    protected function setUp()
    {
        $logger = $this->getMock(LoggerInterface::class);
        $jsonConverter = new JsonConverter($logger);
        $responseData = unserialize('O:8:"stdClass":1:{s:8:"response";a:5:{i:0;O:8:"stdClass":3:{s:2:"id";i:4261;s:10:"error_code";i:602;s:10:"error_desc";s:74:"Some part of the request has not been completed: ad status was not changed";}i:1;O:8:"stdClass":3:{s:2:"id";i:9287;s:10:"error_code";i:602;s:10:"error_desc";s:74:"Some part of the request has not been completed: ad status was not changed";}i:2;O:8:"stdClass":3:{s:2:"id";i:1266;s:10:"error_code";i:602;s:10:"error_desc";s:74:"Some part of the request has not been completed: ad status was not changed";}i:3;O:8:"stdClass":3:{s:2:"id";i:1116;s:10:"error_code";i:602;s:10:"error_desc";s:74:"Some part of the request has not been completed: ad status was not changed";}i:4;O:8:"stdClass":3:{s:2:"id";i:4494;s:10:"error_code";i:602;s:10:"error_desc";s:74:"Some part of the request has not been completed: ad status was not changed";}}}');
        $response = new Response(200, [], $jsonConverter->encode($responseData));
        $transport = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock();
        $transport->expects(static::any())->method('send')->willReturn($response);
        $this->client = new VkontakteClient($transport, $jsonConverter);
    }

    public function testSillyTest()
    {
        $this->client->getCoverage(1, 2, [3], 4);
        $this->client->getGroup('mdk');
        $this->client->updateCampaignsStatus([1, 2], 3, '4', 5);
        $this->client->updateClientAllLimit(1, '2', 3, 4);
    }


}
