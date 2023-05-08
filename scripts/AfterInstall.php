<?php

class AfterInstall
{
    protected $container;

    public function run($container)
    {
        $this->container = $container;

        $entityManager = $container->get('entityManager');
        $this->createScheduledJob($entityManager);
        $this->clearCache();
    }

    protected function clearCache()
    {
        try {
            $this->container->get('dataManager')->clearCache();
        } catch (\Exception $e) {}
    }

    protected function createScheduledJob($entityManager)
    {
        // Check for duplicates
        $existingJob = $entityManager->getRepository('ScheduledJob')->where([
            'job' => 'FileSharingCheckExpiry',
            'name' => 'File Sharing Check Expiry'
        ])->findOne();

        // If there is an existing job with the same name, return
        if ($existingJob) {
            return;
        }

        $job = $entityManager->getNewEntity('ScheduledJob');

        $job->set([
            'job' => 'FileSharingCheckExpiry',
            'name' => 'File Sharing Check Expiry',
            'scheduling' => "*/30 * * * *",
            'isInternal' => false,
            'status' => 'Active',
        ]);

        $entityManager->saveEntity($job);
    }
}