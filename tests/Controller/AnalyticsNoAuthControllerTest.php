<?php
/**
 * Created by anonymous
 * Date: 18/03/19
 * Time: 19:41
 */

namespace App\Tests\Controller;

use App\TestCases\AbstractFunctionalTestCase;

/**
 * Class AnalyticsNoAuthControllerTest
 */
class AnalyticsNoAuthControllerTest extends AbstractFunctionalTestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @return iterable
     */
    public function getAnalyticsObjectKeys(): iterable
    {
        return [
            ['market_price'],
            ['capitalization'],
            ['trade_volume'],
            ['average_block_size'],
            ['average_number_of_tx_per_block'],
            ['median_confirmation_time'],
            ['hashrate_distribution'],
            ['difficulty'],
            ['miners_revenue'],
            ['cost_per_tx_percentage'],
            ['unique_addresses_number'],
            ['mempool_tx_count'],
            ['tx_excluding_popular_number'],
            ['created_at'],
            ['total_bitcoins'],
            ['block_size'],
            ['hash_rate'],
            ['txs_fees'],
            ['mempool_size'],
            ['output_volume'],
            ['pools1'],
            ['pools7'],
            ['n_transactions'],
        ];
    }

    /**
     * @dataProvider getAnalyticsObjectKeys
     *
     * @return void
     *
     * @param string $analyticsObjectKey
     *
     * @throws \Exception
     */
    public function testGetBtcAllTimeAnalyticsWith7DaysReturnResponse(string $analyticsObjectKey): void
    {
        $response = $this->makeJsonRequest(
            'GET',
            "app_analyticsnoauth_getbtcalltimeanalytics",
            [
                'period' => '7days',
            ],
            \json_encode([])
        );

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey($analyticsObjectKey, $data);

        $this->assertNotNull($data[$analyticsObjectKey]);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testGetBtcAllTimePoolAnalyticsWith7DaysReturnResponse(): void
    {
        $response = $this->makeJsonRequest(
            'GET',
            "app_analyticsnoauth_getbtcalltimeanalytics",
            [
                'poolsPeriod' => '7days',
            ],
            \json_encode([])
        );

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);

        if (is_array($data)) {
            foreach ($data as $item) {
                $this->assertArrayHasKey('createdAt', $item);
                $this->assertArrayHasKey('pool', $item);
                $this->assertNotNull($item['createdAt']);
                $this->assertNotNull($item['pool']);
            }
        }
    }
    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testGetBtcAllTimePoolAnalyticsWith30DaysReturnResponse(): void
    {
        $response = $this->makeJsonRequest(
            'GET',
            "app_analyticsnoauth_getbtcalltimeanalytics",
            [
                'poolsPeriod' => '30days',
            ],
            \json_encode([])
        );

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);

        if (is_array($data)) {
            foreach ($data as $item) {
                $this->assertArrayHasKey('createdAt', $item);
                $this->assertArrayHasKey('pool', $item);
                $this->assertNotNull($item['createdAt']);
                $this->assertNotNull($item['pool']);
            }
        }
    }
}
