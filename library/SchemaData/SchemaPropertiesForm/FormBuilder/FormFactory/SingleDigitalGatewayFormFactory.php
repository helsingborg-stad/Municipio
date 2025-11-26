<?php

declare(strict_types=1);

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\FormFactory;

use Municipio\Schema\BaseType;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\MultiSelectField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\StringField;
use WpService\Contracts\__;

/**
 * Class SingleDigitalGatewayFormFactory
 *
 * This class is responsible for creating a form for the SingleDigitalGateway schema.
 */
class SingleDigitalGatewayFormFactory implements FormFactoryInterface
{
    /**
     * Constructor.
     *
     * @param __ $wpService The WordPress service instance.
     */
    public function __construct(
        private __ $wpService,
    ) {}

    /**
     * @inheritDoc
     */
    public function createForm(BaseType $schema): array
    {
        return [
            new StringField(
                'policyCode',
                $this->wpService->__('Policy Code', 'municipio'),
                $schema->getProperty('policyCode'),
                $this->wpService->__(
                    'The policy code(s) that applies to the service provided on the page.',
                    'municipio',
                ),
            ),
            new MultiSelectField(
                'service',
                $this->wpService->__('Service', 'municipio'),
                $schema->getProperty('service'),
                [
                    'information' => $this->wpService->__('Information', 'municipio'),
                    'procedure' => $this->wpService->__('Procedure', 'municipio'),
                ],
                $this->wpService->__(
                    'The type of service or services covered by the page: information, procedure, or support and problem-solving service.',
                    'municipio',
                ),
            ),
            new StringField(
                'policy',
                $this->wpService->__('Policy', 'municipio'),
                $schema->getProperty('policy'),
                $this->wpService->__('The policy that is applied to the service provided on the page.', 'municipio'),
            ),
            new StringField(
                'location',
                $this->wpService->__('Location', 'municipio'),
                $schema->getProperty('location'),
                $this->wpService->__('The location where the service is provided.', 'municipio'),
            ),
        ];
    }
}
