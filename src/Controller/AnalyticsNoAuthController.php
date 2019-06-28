<?php
/**
 * Created by anonymous
 * Date: 05/03/19
 * Time: 14:11
 */

namespace App\Controller;

use App\Entity\CertoreAllTimeAnalytics;
use App\Entity\CertoreMonthlyAnalytics;
use App\Entity\CertorePoolAnalytics;
use App\Entity\CertoreStableAnalytics;
use App\Entity\CertoreWeeklyAnalytics;
use App\Entity\Wallet;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Class AnalyticsController
 */
class AnalyticsNoAuthController extends AbstractFOSRestController implements ApiNoAuthInterface
{
    /**
     * @param int $batch
     *
     * @return iterable|null
     *
     * @Rest\Get("/v1.0/wallets&latest-top-vin?batch={batch}")
     *
     * @Rest\View(serializerGroups={"latest"}, statusCode=200)
     *
     * @throws \Exception
     */
    public function getLatestTopVinWallets(int $batch): ?iterable
    {
        $entityManager = $this->get('doctrine')->getManager();

        return $entityManager->getRepository(Wallet::class)->findLatestVinByBatch($batch);
    }

    /**
     * @param int $batch
     *
     * @return iterable|null
     *
     * @Rest\Get("/v1.0/wallets&latest-top-vout?batch={batch}")
     *
     * @Rest\View(serializerGroups={"latest"}, statusCode=200)
     *
     * @throws \Exception
     */
    public function getLatestTopVoutWallets(int $batch): ?iterable
    {
        $entityManager = $this->get('doctrine')->getManager();

        return $entityManager->getRepository(Wallet::class)->findLatestVoutByBatch($batch);
    }

    /**
     * @param int $batch
     *
     * @return iterable|null
     *
     * @Rest\Get("/v1.0/wallets&top-max?batch={batch}")
     *
     * @Rest\View(serializerGroups={"latest"}, statusCode=200)
     *
     * @throws \Exception
     */
    public function getTopVinWallets(int $batch): ?iterable
    {
        $entityManager = $this->get('doctrine')->getManager();

        return $entityManager->getRepository(Wallet::class)->findTopVinByBatch($batch);
    }

    /**
     * @param int $batch
     *
     * @return iterable|null
     *
     * @Rest\Get("/v1.0/wallets&top-min?batch={batch}")
     *
     * @Rest\View(serializerGroups={"latest"}, statusCode=200)
     *
     * @throws \Exception
     */
    public function getTopVoutWallets(int $batch): ?iterable
    {
        $entityManager = $this->get('doctrine')->getManager();

        return $entityManager->getRepository(Wallet::class)->findTopVoutByBatch($batch);
    }
}
