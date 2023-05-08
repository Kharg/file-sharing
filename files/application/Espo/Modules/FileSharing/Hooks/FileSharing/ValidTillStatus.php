<?php

namespace Espo\Modules\FileSharing\Hooks\FileSharing;

use Espo\Core\Hooks\Base;
use Espo\ORM\Entity;

class ValidTillStatus extends Base
{
    public function beforeSave(Entity $entity, array $options = [])
    {
        $validTill = $entity->get('validTill');
        $status = $entity->get('status');
        $now = new \DateTime();

        if ($validTill) {
            $validTillDateTime = new \DateTime($validTill);
            $serverTimezone = new \DateTimeZone(date_default_timezone_get());
            $validTillDateTime->setTimezone($serverTimezone);

            if ($validTillDateTime < $now && $status === 'Active') {
                $entity->set('status', 'Expired');
            } elseif ($validTillDateTime >= $now && $status === 'Expired') {
                $entity->set('status', 'Active');
            }
        }
    }
}
