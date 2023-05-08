<?php

namespace Espo\Modules\FileSharing\Controllers;

use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\NotFound;

class GenerateToken
{
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function postActionGenerateNewToken(Request $request, Response $response): void
    {
        $data = $request->getParsedBody();

        if (empty($data->id)) {
            throw new BadRequest('ID is missing.');
        }

        $id = $data->id;

        // Load the FileSharing entity
        $fileSharing = $this->entityManager->getEntity('FileSharing', $id);
        if (!$fileSharing) {
            throw new NotFound("FileSharing entity with ID '{$id}' not found.");
        }

        // Generate a new access token
        $accessToken = bin2hex(random_bytes(18));

        // Set the new access token to the FileSharing entity
        $fileSharing->set('accessToken', $accessToken);

        // Save the FileSharing entity with the new access token
        $this->entityManager->saveEntity($fileSharing);

        // Return the new access token in the response
        $response->writeBody(json_encode(['accessToken' => $accessToken]));
    }
}