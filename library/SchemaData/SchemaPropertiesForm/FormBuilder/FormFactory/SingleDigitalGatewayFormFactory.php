<?php

declare(strict_types=1);

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\FormFactory;

use Municipio\Schema\BaseType;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\MultiSelectField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\StringField;
use WpService\Contracts\__;
use WpService\Contracts\_x;

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
     * @param __&_x $wpService The WordPress service instance.
     */
    public function __construct(
        private __&_x $wpService,
    ) {}

    /**
     * @inheritDoc
     */
    public function createForm(BaseType $schema): array
    {
        return [
            new StringField(
                'policyCode',
                $this->wpService->_x('Policy Code', 'SingleDigitalGateway', 'municipio'),
                $schema->getProperty('policyCode'),
                $this->wpService->_x(
                    'The policy code(s) that applies to the service provided on the page.',
                    'SingleDigitalGateway',
                    'municipio',
                ),
            ),
            new MultiSelectField(
                'service',
                $this->wpService->__('Service', 'municipio'),
                $schema->getProperty('service'),
                [
                    'information' => $this->wpService->_x('Information', 'SingleDigitalGateway', 'municipio'),
                    'procedure' => $this->wpService->_x('Procedure', 'SingleDigitalGateway', 'municipio'),
                ],
                $this->wpService->_x(
                    'The type of service or services covered by the page: information, procedure, or support and problem-solving service.',
                    'SingleDigitalGateway',
                    'municipio',
                ),
            ),
            new StringField(
                'policy',
                $this->wpService->_x('Policy', 'SingleDigitalGateway', 'municipio'),
                $schema->getProperty('policy'),
                $this->wpService->_x(
                    'The policy that is applied to the service provided on the page.',
                    'SingleDigitalGateway',
                    'municipio',
                ),
            ),
            new StringField(
                'location',
                $this->wpService->_x('Location', 'SingleDigitalGateway', 'municipio'),
                $schema->getProperty('location'),
                $this->wpService->_x(
                    'The location where the service is provided.',
                    'SingleDigitalGateway',
                    'municipio',
                ),
            ),
        ];
    }
}
