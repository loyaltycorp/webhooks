<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Webhooks\Unit\Bridge\Laravel\Providers;

use Doctrine\ORM\EntityManagerInterface;
use EoneoPay\Externals\EventDispatcher\Interfaces\EventDispatcherInterface as RealEventDispatcher;
use EoneoPay\Externals\HttpClient\Interfaces\ClientInterface as HttpClientInterface;
use EoneoPay\Externals\Logger\Interfaces\LoggerInterface;
use EoneoPay\Externals\Logger\Logger;
use EoneoPay\Utils\Interfaces\XmlConverterInterface;
use EoneoPay\Utils\XmlConverter;
use EoneoPay\Webhooks\Activity\Interfaces\ActivityManagerInterface;
use EoneoPay\Webhooks\Bridge\Doctrine\Handlers\Interfaces\RequestHandlerInterface;
use EoneoPay\Webhooks\Bridge\Doctrine\Handlers\Interfaces\ResponseHandlerInterface;
use EoneoPay\Webhooks\Bridge\Laravel\Providers\WebhookServiceProvider;
use EoneoPay\Webhooks\Events\Interfaces\EventDispatcherInterface;
use EoneoPay\Webhooks\Persister\Interfaces\WebhookPersisterInterface;
use EoneoPay\Webhooks\Subscription\Interfaces\SubscriptionRetrieverInterface;
use Illuminate\Container\Container;
use Tests\EoneoPay\Webhooks\Stubs\Externals\EventDispatcherStub;
use Tests\EoneoPay\Webhooks\Stubs\Externals\HttpClientStub;
use Tests\EoneoPay\Webhooks\Stubs\Subscription\SubscriptionRetrieverStub;
use Tests\EoneoPay\Webhooks\Stubs\Vendor\Doctrine\Common\Persistence\ManagerRegistryStub;
use Tests\EoneoPay\Webhooks\Stubs\Vendor\Doctrine\ORM\EntityManagerStub;
use Tests\EoneoPay\Webhooks\WebhookTestCase;

/**
 * @covers \EoneoPay\Webhooks\Bridge\Laravel\Providers\WebhookServiceProvider
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Test case only, high coupling required to fully test service provider
 */
class WebhookServiceProviderTest extends WebhookTestCase
{
    /**
     * @var \Illuminate\Container\Container|null
     */
    private $app;

    /**
     * Returns interfaces that should be registered in the
     * container.
     *
     * @return string[][]
     */
    public function getRegisteredInterfaces(): array
    {
        return [
            'ActivityManagerInterface' => [ActivityManagerInterface::class],
            'EventDispatcherInterface' => [EventDispatcherInterface::class],
            'RealEventDispatcherInterface' => [RealEventDispatcher::class],
            'RequestHandlerInterface' => [RequestHandlerInterface::class],
            'ResponseHandlerInterface' => [ResponseHandlerInterface::class],
            'WebhookPersisterInterface' => [WebhookPersisterInterface::class]
        ];
    }

    /**
     * Test provider register container.
     *
     * @param string $interface
     *
     * @return void
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @dataProvider getRegisteredInterfaces
     */
    public function testRegister(string $interface): void
    {
        $app = $this->getApplication();

        self::assertInstanceOf($interface, $app->get($interface));
    }

    /**
     * Get application instance
     *
     * @return \Illuminate\Container\Container
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function getApplication(): Container
    {
        // If app already exists, return
        if (($this->app instanceof Container) === true) {
            return $this->app;
        }

        $app = $this->createApplication();
        $app->bind(SubscriptionRetrieverInterface::class, SubscriptionRetrieverStub::class);
        $app->bind(RealEventDispatcher::class, EventDispatcherStub::class);
        $app->bind(XmlConverterInterface::class, XmlConverter::class);
        $app->bind(HttpClientInterface::class, HttpClientStub::class);
        $app->bind(LoggerInterface::class, Logger::class);

        $app->bind(EntityManagerInterface::class, EntityManagerStub::class);
        $app->bind('registry', ManagerRegistryStub::class);

        /** @noinspection PhpParamsInspection Lumen application is a foundation application */
        $provider = new WebhookServiceProvider($app);
        $provider->register();

        return $this->app = $app;
    }
}
