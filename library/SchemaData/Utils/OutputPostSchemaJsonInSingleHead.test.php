<?php

namespace Municipio\SchemaData\Utils;

use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface;
use PHPUnit\Framework\TestCase;
use Municipio\Schema\BaseType;
use Municipio\Schema\Thing;
use WP_Post;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetPost;
use WpService\Contracts\IsSingle;

class OutputPostSchemaJsonInSingleHeadTest extends TestCase {
    /**
     * @testdox Runs print on wp_head hook.
     */
    public function testHooks(): void {
        $wpService = $this->getWpService();
        $outputPostSchemaJsonInSingleHead = new OutputPostSchemaJsonInSingleHead($this->getSchemaObjectFromPost(), $wpService);
        $outputPostSchemaJsonInSingleHead->addHooks();

        $this->assertEquals('wp_head', $wpService->calls['addAction'][0][0]);
        $this->assertEquals([$outputPostSchemaJsonInSingleHead, 'print'], $wpService->calls['addAction'][0][1]);
    }

    public function testPrintsIfOnSingle(): void {
        $wpService = $this->getWpService(['isSingle' => true]);
        $outputPostSchemaJsonInSingleHead = new OutputPostSchemaJsonInSingleHead($this->getSchemaObjectFromPost(), $wpService);
        
        ob_start();
        $outputPostSchemaJsonInSingleHead->print();

        $this->assertEquals('PrintedSchemaObject', ob_get_clean());
    }
    
    public function testDoesNotPrintIfNotOnSingle(): void {
        $wpService = $this->getWpService(['isSingle' => false]);
        $outputPostSchemaJsonInSingleHead = new OutputPostSchemaJsonInSingleHead($this->getSchemaObjectFromPost(), $wpService);
        
        ob_start();
        $outputPostSchemaJsonInSingleHead->print();

        $this->assertEquals('', ob_get_clean());
    }

    private function getSchemaObjectFromPost(): SchemaObjectFromPostInterface {
        return new class implements SchemaObjectFromPostInterface {
            public function create(WP_Post $post): BaseType
            {
                return new class extends Thing {
                    public function toScript(): string
                    {
                        return 'PrintedSchemaObject';
                    }
                };
            }
        };
    }

    private function getWpService(array $db = []): AddAction&IsSingle&GetPost {
        return new class ($db) implements AddAction, IsSingle, GetPost {
            public array $calls = ['addAction' => []];
            public function __construct(private array $db)
            {
            }
            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->calls['addAction'][] = func_get_args();
                return true;
            }
            public function isSingle($post = ''): bool
            {
                return $this->db['isSingle'] ?? false;
            }
            public function getPost(null|int|WP_Post $post = null, string $output = 'OBJECT', string $filter = "raw"): WP_Post|array|null
            {
                return new WP_Post((object) ['ID' => 1]);
            }
        };
    }
}