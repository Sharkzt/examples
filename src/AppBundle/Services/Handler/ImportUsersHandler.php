<?php
/**
 * Created by anonymous
 * Date: 26/07/18
 * Time: 09:43
 */

namespace AppBundle\Services\Handler;

use AppBundle\Entity\PlusUser;

/**
 * Class ImportUsersHandler
 */
class ImportUsersHandler extends AbstractImportHandler
{

    /**
     * @param string        $source
     * @param PlusUser|null $user
     *
     * @return AbstractImportHandler
     */
    public function handle(string $source, PlusUser $user = null): AbstractImportHandler
    {
        $data = [];
        $fields = ['email' => 'string', 'datetime' => 'string', 'isImported' => 'string', 'error' => 'string'];

        if (0 < mb_strlen($source)) {
            try {
                foreach ($this->getEmailsFromString()->parse($source)->getEmails() as $email) {
                    try {
                        $this->getInvitationCreator()->createByEmail($email);
                        $data[] = ['email' => $email, 'datetime' => new \DateTime(), 'isImported' => 'yes', 'error' => ''];
                    } catch (\Exception $e) {
                        $this->getLogger()->warning('Error during users import: '.$e->getMessage());
                        $data[] = ['email' => $email, 'datetime' => new \DateTime(), 'isImported' => 'no', 'error' => $e->getMessage()];
                    } catch (\Error $e) {
                        $this->getLogger()->alert('Error during users import: '.$e->getMessage());
                        $data[] = ['email' => $email, 'datetime' => new \DateTime(), 'isImported' => 'no', 'error' => $e->getMessage()];
                    }
                }
            } catch (\Error $e) {
                $this->getLogger()->alert('Error during users import: '.$e->getMessage());
            }
        }

        if (!$this->getTokenStorage()->getToken() || !$this->getTokenStorage()->getToken()->getUser()) {
            throw new \BadMethodCallException("User should be authenticated");
        }

        $this
            ->generateReport($fields, $data)
            ->getEmailSender()
            // fixme
            ->sendEmailByAddress($this->getTokenStorage()->getToken()->getUser()->getEmail(), 'users_import_report', 'en_US', ['attachments' => [$this->getXlsGenerator()->getFullFilePath()]]);

        return $this;
    }
}
