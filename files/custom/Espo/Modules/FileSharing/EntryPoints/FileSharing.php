<?php

namespace Espo\Modules\FileSharing\EntryPoints;

use Espo\Core\Exceptions\NotFound;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\EntryPoints\Base;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Entities\Attachment as AttachmentEntity;
use Espo\Core\FileStorage\Manager as FileStorageManager;
use Espo\Core\EntryPoint\Traits\NoAuth;

class FileSharing extends Base
{
    use NoAuth;

    protected function getEntityManager(): EntityManager
    {
        return $this->getContainer()->get('entityManager');
    }

    protected function fileStorageManager(): fileStorageManager
    {
        return $this->getContainer()->get('fileStorageManager');
    }

    public function run(Request $request, Response $response): void
    {
        $id = $request->getQueryParam('id');
        if (!$id) {
            throw new BadRequest("Missing 'id' parameter.");
        }
        
        $fileSharing = $this->getEntityManager()->getEntity('FileSharing', $id);
        if (!$fileSharing) {
            throw new NotFound("FileSharing entity with ID '{$id}' not found.");
        }

        // Check if the accessToken matches the one in the entity
        $accessToken = $request->getQueryParam('token');
        if (!$accessToken || $accessToken !== $fileSharing->get('accessToken')) {
            throw new Forbidden("Token not valid.");
        }
    
        $status = $fileSharing->get('status');
    if ($status !== 'Active') {
        throw new Forbidden("The requested file is not active and cannot be downloaded.");
    }

    $validTill = $fileSharing->get('validTill');
    if ($validTill) {
        // Convert validTill to the server's timezone
        $validTillDateTime = new \DateTime($validTill);
        $serverTimezone = new \DateTimeZone(date_default_timezone_get());
        $validTillDateTime->setTimezone($serverTimezone);

        $now = new \DateTime();
        if ($now > $validTillDateTime) {
            throw new Forbidden("The requested file has expired and is no longer available.");
        }
    }

        $attachmentId = $fileSharing->get('attachmentId');
        if (!$attachmentId) {
            throw new NotFound("Attachment not found for FileSharing entity with ID '{$id}'.");
        }
        $attachment = $this->getEntityManager()->getEntityById(AttachmentEntity::ENTITY_TYPE, $attachmentId);
        $stream = $this->fileStorageManager()->getStream($attachment);
        if (!$attachment) {
            throw new NotFound("Attachment with ID '{$attachmentId}' not found.");
        }

        $filePath = $this->getEntityManager()->getRepository('Attachment')->getFilePath($attachment);
        if (!$filePath) {
            throw new NotFound("File not found on the server.");
        }

        $fileName = $attachment->getName();
        $fileType = $attachment->getType();
        $size = $stream->getSize() ?? $this->fileStorageManager()->getSize($attachment);
        $forceFileDownload = $fileSharing->get('forceFileDownload');
    
     $accessCount = $fileSharing->get('accessCount');
    $allowedUsage = $fileSharing->get('allowedUsage');
    if ($allowedUsage != 0 && $accessCount >= $allowedUsage) {
        throw new NotFound("Download Limit exceeded.");
    }
     // Increment the accessCount field
    $accessCount = $fileSharing->get('accessCount');
    $fileSharing->set('accessCount', $accessCount + 1);
    $this->getEntityManager()->saveEntity($fileSharing);

        $this->downloadFile($response, $stream, $fileName, $fileType, $forceFileDownload, $size);
    }

    protected function downloadFile(Response $response, $stream, $fileName, $fileType, $forceFileDownload, $size)
    {
        $disposition = $forceFileDownload ? 'attachment' : 'inline';

        $response->setHeader("Content-Disposition", "$disposition; filename=\"{$fileName}\"");
        $response->setHeader("Content-Type", $fileType);
        $response->setHeader('Pragma', 'public');
        $response->setHeader('Cache-Control', 'must-revalidate');
        $response->setHeader('Expires', '0');
        $response->setHeader('Content-Length', (string) $size);
        $response->setBody($stream);
    }
}