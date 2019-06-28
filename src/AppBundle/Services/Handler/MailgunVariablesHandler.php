<?php
/**
 * Created by anonymous
 * Date: 08/03/18
 * Time: 09:43
 */

namespace AppBundle\Services\Handler;

use AppBundle\Services\CurrentSiteGetter;
use AppBundle\Structs\MailgunVariables;

/**
 * Class MailgunVariablesHandler
 */
class MailgunVariablesHandler
{
    /**
     * @var MailgunVariables
     */
    protected $mailgunVariables;

    /**
     * @var CurrentSiteGetter
     */
    protected $currentSiteGetter;

    /**
     * @return MailgunVariables
     */
    public function getMailgunVariables(): MailgunVariables
    {
        return $this->mailgunVariables;
    }

    /**
     * @param MailgunVariables $mailgunVariables
     *
     * @return MailgunVariablesHandler
     */
    public function setMailgunVariables(MailgunVariables $mailgunVariables): MailgunVariablesHandler
    {
        $this->mailgunVariables = $mailgunVariables;
        $this->mailgunVariables
            ->setDistributionName($this->getCurrentSiteGetter()->getDistributionName());

        return $this;
    }

    /**
     * @return CurrentSiteGetter
     */
    public function getCurrentSiteGetter(): CurrentSiteGetter
    {
        return $this->currentSiteGetter;
    }

    /**
     * @param CurrentSiteGetter $currentSiteGetter
     *
     * @return MailgunVariablesHandler
     */
    public function setCurrentSiteGetter(CurrentSiteGetter $currentSiteGetter): MailgunVariablesHandler
    {
        $this->currentSiteGetter = $currentSiteGetter;

        return $this;
    }
}
