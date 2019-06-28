<?php
/**
 * Created by anonymous
 * Date: 24/07/18
 * Time: 18:49
 */

namespace AppBundle\Services\Handler;

/**
 * Class EmailsFromStringHandler
 */
class EmailsFromStringHandler
{
    /**
     * @var iterable
     */
    private $emails = [];

    /**
     * Method do not perform emails validation
     *
     * @param string $emails
     *
     * @return EmailsFromStringHandler
     */
    public function parse(string $emails): EmailsFromStringHandler
    {
        $this->setEmails(preg_split('/\s+/', trim($emails)));

        return $this;
    }

    /**'
     * @param iterable|array $emails
     *
     * @return EmailsFromStringHandler
     */
    public function setEmails(iterable $emails): EmailsFromStringHandler
    {
        $this->emails = $emails;

        return $this;
    }

    /**
     * @param string $email
     *
     * @return EmailsFromStringHandler
     */
    public function addEmail(string $email): EmailsFromStringHandler
    {
        $this->emails[] = $email;

        return $this;
    }

    /**
     * @return iterable
     */
    public function getEmails(): iterable
    {
        if (0 === sizeof($this->emails)) {
            throw new \LogicException(sprintf("No emails parsed from input string"));
        }

        return $this->emails;
    }
}
