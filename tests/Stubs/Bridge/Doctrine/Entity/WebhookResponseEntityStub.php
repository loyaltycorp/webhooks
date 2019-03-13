<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Webhooks\Stubs\Bridge\Doctrine\Entity;

use EoneoPay\Externals\HttpClient\Interfaces\ResponseInterface;
use EoneoPay\Webhooks\Bridge\Doctrine\Entity\WebhookEntityInterface;
use EoneoPay\Webhooks\Bridge\Doctrine\Entity\WebhookResponseEntityInterface;
use Illuminate\Support\Collection;

class WebhookResponseEntityStub implements WebhookResponseEntityInterface
{
    /**
     * @var \Illuminate\Support\Collection
     */
    private $data;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->data = new Collection();
    }

    /**
     * Returns data
     *
     * @return \Illuminate\Support\Collection
     */
    public function getData(): Collection
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function populate(WebhookEntityInterface $webhook, ResponseInterface $response): void
    {
        $this->data['webhook'] = $webhook;
        $this->data['response'] = $response;
    }
}
