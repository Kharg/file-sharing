<?php

namespace Espo\Modules\FileSharing\Hooks\FileSharing;

use Espo\ORM\Entity;

class GenerateDownloadToken
{
    public function beforeSave(Entity $entity, array $options = [])
    {
        // Generate a random 36-character alphanumeric token
        $accessToken = bin2hex(random_bytes(18));

        if ($entity->isNew()) {
            $entity->set('accessToken', $accessToken);
        }
    }
}
