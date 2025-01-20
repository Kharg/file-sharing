<?php

namespace Espo\Modules\FileSharing\Jobs;

use DateTime;
use DateTimeZone;
use Espo\Core\Job\Job;
use Espo\Core\Job\Job\Data;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Utils\Config;
use Exception;

class FileSharingCheckExpiry implements Job
{
    private EntityManager $entityManager;
    private Config $config;

    public function __construct(EntityManager $entityManager, Config $config)
    {
        $this->entityManager = $entityManager;
        $this->config = $config;
    }

    /**
     * @throws Exception
     */
    public function run(Data $data): void
    {
        $now = new DateTime('now', new DateTimeZone('UTC'));

        $fileSharingList = $this->entityManager->getRepository('FileSharing')->where([
            'validTill!=' => null,
            'validTill<' => $now->format('Y-m-d H:i:s'),
            'status' => 'Active',
        ])->find();

        foreach ($fileSharingList as $fileSharing) {
            $fileSharing->set('status', 'Expired');
            $this->entityManager->saveEntity($fileSharing, ['silent' => true]);
        }
    }
}