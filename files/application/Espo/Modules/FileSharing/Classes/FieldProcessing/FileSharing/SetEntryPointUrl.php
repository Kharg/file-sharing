<?php

namespace Espo\Modules\FileSharing\Classes\FieldProcessing\FileSharing;

use Espo\Core\FieldProcessing\Loader;
use Espo\Core\FieldProcessing\Loader\Params;
use Espo\Core\ORM\EntityManager;
use Espo\ORM\Entity;
use Espo\Core\Utils\Config;

class SetEntryPointUrl implements Loader
{
    private EntityManager $entityManager;
    private Config $config;

    public function __construct(EntityManager $entityManager, Config $config)
    {
        $this->entityManager = $entityManager;
        $this->config = $config;
    }

    public function process(Entity $entity, Params $params): void
    {
        $id = $entity->getId();
        $token = $entity->get('accessToken');

        $siteUrl = $this->config->get('siteUrl');

        // Add a '/' at the end of the siteUrl if there's none
        if (substr($siteUrl, -1) !== '/') {
            $siteUrl .= '/';
        }

        $entryPointUrl = "{$siteUrl}?entryPoint=FileSharing&id={$id}&token={$token}";

        $entity->set('entryPointUrl', $entryPointUrl);
    }
}
