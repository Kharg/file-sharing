<?php

class AfterUninstall
{
    protected $container;

    public function run($container)
    {
        $this->container = $container;

        $entityManager = $container->get('entityManager');
        $this->removeScheduledJob($entityManager);
        $this->clearCache();
    }

    protected function clearCache()
    {
        try {
            $this->container->get('dataManager')->clearCache();
        } catch (\Exception $e) {}
    }

    protected function removeScheduledJob($entityManager)
    {
        $scheduledJob = $entityManager->getRepository('ScheduledJob')->where([
            'job' => 'FileSharingCheckExpiry',
            'name' => 'File Sharing Check Expiry'
        ])->findOne();

        if ($scheduledJob) {
            $entityManager->removeEntity($scheduledJob);
        }
    }
}
