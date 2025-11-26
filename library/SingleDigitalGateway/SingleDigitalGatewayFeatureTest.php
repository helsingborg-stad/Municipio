<?php

namespace Municipio\SingleDigitalGateway;

use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddAction;

class SingleDigitalGatewayFeatureTest extends TestCase
{
    #[TestDox('Enables feature by adding wp action to print meta tags in head')]
    public function testEnableAddsAction(): void
    {
        $wpService = new class implements AddAction {
            public array $addedActions = [];

            public function addAction(
                string $hookName,
                callable $callback,
                int $priority = 10,
                int $acceptedArgs = 1,
            ): true {
                $this->addedActions[] = [
                    'hookName' => $hookName,
                    'callback' => $callback,
                    'priority' => $priority,
                    'acceptedArgs' => $acceptedArgs,
                ];
                return true;
            }
        };

        $feature = new SingleDigitalGatewayFeature($wpService);
        $feature->enable();

        $this->assertCount(1, $wpService->addedActions);
        $this->assertEquals(
            'Municipio\SchemaData\OutputPostSchemaJsonInSingleHead\Print',
            $wpService->addedActions[0]['hookName'],
        );
    }

    #[TestDox('Prints meta tags when schema type is SingleDigitalGateway')]
    public function testPrintMetaTagsOutputsMetaTags(): void
    {
        $feature = new SingleDigitalGatewayFeature($this->createMock(AddAction::class));

        // Capture the output
        ob_start();
        $feature->printMetaTags(Schema::singleDigitalGateway()->policyCode('Policy123'));
        $output = ob_get_clean();

        $this->assertStringContainsString('<meta', $output);
    }
}
