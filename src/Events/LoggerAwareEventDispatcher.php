<?php
declare(strict_types=1);

namespace EoneoPay\Webhooks\Events;

use EoneoPay\Externals\Logger\Interfaces\LoggerInterface;
use EoneoPay\Webhooks\Events\Interfaces\EventDispatcherInterface;

final class LoggerAwareEventDispatcher implements EventDispatcherInterface
{
    /**
     * @var \EoneoPay\Webhooks\Events\Interfaces\EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var \EoneoPay\Externals\Logger\Interfaces\LoggerInterface
     */
    private $logger;

    /**
     * Constructor
     *
     * @param \EoneoPay\Webhooks\Events\Interfaces\EventDispatcherInterface $dispatcher
     * @param \EoneoPay\Externals\Logger\Interfaces\LoggerInterface $logger
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger
    ) {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function activityCreated(int $activityId): void
    {
        $this->logger->info('Activity Created', [
            'activity_id' => $activityId
        ]);

        $this->dispatcher->activityCreated($activityId);
    }

    /**
     * {@inheritdoc}
     */
    public function webhookRequest(int $requestId): void
    {
        $this->logger->info('Webhook Request Created', [
            'request_id' => $requestId
        ]);

        $this->dispatcher->webhookRequest($requestId);
    }
}
