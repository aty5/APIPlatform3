<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;

class OpenApiFactory implements OpenApiFactoryInterface//
{

    public function __construct(private OpenApiFactoryInterface $openApiFactory)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->openApiFactory->__invoke($context);

        /**
         * @var PathItem $path
         */
        foreach ($openApi->getPaths()->getPaths() as $key => $path) {
            if ($path->getGet() && $path->getGet()->getSummary() == 'hidden') {
                $openApi->getPaths()->addPath($key, $path->withGet(null));
            }
        }

        $openApi->getPaths()->addPath('/ping', new PathItem(null, 'Ping', null, new Operation('ping-id', [], [], 'Répond')));
        return $openApi;
    }
}