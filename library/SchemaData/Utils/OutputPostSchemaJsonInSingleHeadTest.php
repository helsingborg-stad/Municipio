<?php

namespace Municipio\SchemaData\Utils;

use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use Municipio\Schema\Thing;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Post;
use WpService\Contracts\AddAction;
use WpService\Contracts\DoAction;
use WpService\Contracts\GetPost;
use WpService\Contracts\IsSingle;
use WpService\Implementations\FakeWpService;

class OutputPostSchemaJsonInSingleHeadTest extends TestCase
{
    #[TestDox('Runs print on wp_head hook.')]
    public function testHooks(): void
    {
        $wpService = $this->getWpService();
        $outputPostSchemaJsonInSingleHead = new OutputPostSchemaJsonInSingleHead(
            $this->getSchemaObjectFromPost(),
            $wpService,
        );
        $outputPostSchemaJsonInSingleHead->addHooks();

        $this->assertEquals('wp_head', $wpService->calls['addAction'][0][0]);
        $this->assertEquals([$outputPostSchemaJsonInSingleHead, 'print'], $wpService->calls['addAction'][0][1]);
    }

    #[TestDox('Prints schema JSON if on single.')]
    public function testPrintsIfOnSingle(): void
    {
        $wpService = $this->getWpService(['isSingle' => true]);
        $outputPostSchemaJsonInSingleHead = new OutputPostSchemaJsonInSingleHead(
            $this->getSchemaObjectFromPost(),
            $wpService,
        );

        ob_start();
        $outputPostSchemaJsonInSingleHead->print();

        $this->assertEquals('PrintedSchemaObject', ob_get_clean());
    }

    #[TestDox('Does not print if not on single.')]
    public function testDoesNotPrintIfNotOnSingle(): void
    {
        $wpService = $this->getWpService(['isSingle' => false]);
        $outputPostSchemaJsonInSingleHead = new OutputPostSchemaJsonInSingleHead(
            $this->getSchemaObjectFromPost(),
            $wpService,
        );

        ob_start();
        $outputPostSchemaJsonInSingleHead->print();

        $this->assertEquals('', ob_get_clean());
    }

    #[TestDox('Trigger action for printed schema.')]
    public function testTriggersActions(): void
    {
        $wpService = new FakeWpService([
            'isSingle' => true,
            'getPost' => new WP_Post([]),
        ]);
        $schema = Schema::thing()->name('Test Thing');
        $outputPostSchemaJsonInSingleHead = new OutputPostSchemaJsonInSingleHead(
            $this->getSchemaObjectFromPost($schema),
            $wpService,
        );

        ob_start();
        $outputPostSchemaJsonInSingleHead->print();
        ob_end_clean();

        $this->assertEquals(
            [
                ['Municipio\SchemaData\OutputPostSchemaJsonInSingleHead\Print', $schema],
            ],
            $wpService->methodCalls['doAction'],
        );
    }

    private function getSchemaObjectFromPost(null|BaseType $schema = null): SchemaObjectFromPostInterface
    {
        if (is_null($schema)) {
            $schema = $this->getSchema();
        }
        return new class($schema) implements SchemaObjectFromPostInterface {
            public function __construct(
                private BaseType $schema,
            ) {}

            public function create(WP_Post|PostObjectInterface $post): BaseType
            {
                return $this->schema;
            }
        };
    }

    private function getSchema(): BaseType
    {
        return new class extends Thing {
            public function toScript(): string
            {
                return 'PrintedSchemaObject';
            }
        };
    }

    private function getWpService(array $db = []): AddAction&IsSingle&GetPost&DoAction
    {
        return new class($db) implements AddAction, IsSingle, GetPost, DoAction {
            public array $calls = ['addAction' => []];

            public function __construct(
                private array $db,
            ) {}

            public function addAction(
                string $hookName,
                callable $callback,
                int $priority = 10,
                int $acceptedArgs = 1,
            ): true {
                $this->calls['addAction'][] = func_get_args();
                return true;
            }

            public function isSingle($post = ''): bool
            {
                return $this->db['isSingle'] ?? false;
            }

            public function getPost(
                null|int|WP_Post $post = null,
                string $output = 'OBJECT',
                string $filter = 'raw',
            ): WP_Post|array|null {
                return new WP_Post((object) ['ID' => 1]);
            }

            public function doAction(string $hookName, mixed ...$arg): void
            {
            }
        };
    }
}
