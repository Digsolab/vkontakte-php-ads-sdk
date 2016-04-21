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
        $responseData = ['response'=>['fooobar','error'=>'OK']];
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
