<?php

namespace Api\Controller\Chart;

use Api\Entities\User;
use Api\Service\Chart\Pie;
use Api\Service\Transaction\Date;
use Api\Service\AccessorTrait;

/**
 * Class AddController
 *
 * @package Api\Controller\Pie
 *
 * @method PieController setDate(Date $date)
 * @method PieController setChartPie(Pie $data)
 * @method Date          getDate()
 * @method Pie           getChartPie()
*/
class PieController
{
    use AccessorTrait;

    /**
     * @param User   $user
     * @param array  $connectedUserIds
     * @param string $currency
     * @param string $from
     * @param string $till
     *
     * @return array
     */
    public function getResponse(
        User $user,
        array $connectedUserIds,
        $currency,
        $from,
        $till
    ) {
        $userIds = array_merge(array($user->getId()), $connectedUserIds);

        $date = $this->getDate();
        $dateFrom = !empty($from) ? $date->getDateTime($user, $from) : null;
        $dateTill = !empty($till) ? $date->getDateTime($user, $till) : null;

        $response = array(
            'success'  => true,
            'count'    => 0,
            'currency' => $currency,
            'from'     => $dateFrom ? $dateFrom->getTimestamp() : null,
            'till'     => $dateTill ? $dateTill->getTimestamp() : null,
            'data'     => array(),
        );

        try {
            $this->checkData($currency);

            $chartPie       = $this->getChartPie();
            $data           = $chartPie->getData($userIds, $currency, $dateFrom, $dateTill);
            $percentData    = $chartPie->addPercent($data);
            $sortedData     = $chartPie->sortByPercent($percentData);
            $normalizedData = $chartPie->normalizeResults($sortedData, $user, $currency);

            $response['count'] = count($normalizedData);
            $response['data']  = $normalizedData;
        } catch (\InvalidArgumentException $exc) {
            $response['message'] = $exc->getMessage();
            $response['success'] = false;
        }

        return $response;
    }

    /**
     * @param $currency
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    private function checkData($currency)
    {
        if (empty($currency)) {
            throw new \InvalidArgumentException('Currency cannot be empty');
        }

        return $this;
    }
}
