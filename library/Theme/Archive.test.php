<?php

namespace Municipio\Theme;

use Municipio\Helper\WpService;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ArchiveTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        WpService::set(new FakeWpService(['addAction' => true]));
        $archive = new Archive();
        $this->assertInstanceOf(Archive::class, $archive);
    }

    /**
     * @testdox addOrderByFallback() appends ID to orderby to avoid inconsistency in ordering
     * @dataProvider provideOrderByMatch
     */
    public function testAddOrderByFallbackAppendsIdToOrderbyToAvoidInconsistencyInOrdering($orderBy)
    {
        WpService::set(new FakeWpService(['addAction' => true]));
        $query = $this->getWpQuery();
        $query->set('orderby', $orderBy);
        $archive = new Archive();

        $archive->addOrderByFallback($query);

        $this->assertContains('ID', $query->get('orderby'));
    }

    public function provideOrderByMatch()
    {
        return [
            'date'                                       => ['date'],
            'modified'                                   => ['modified'],
            'array with one value (date)'                => [['date']],
            'array with one value (modified)'            => [['modified']],
            'array with key and value (date => DESC)'    => [['date' => 'DESC']],
            'array with key and value (modified => ASC)' => [['modified' => 'ASC']],
        ];
    }

    /**
     * @testdox addOrderByFallback() ensures that order is preserved when appending ID to orderby
     * @dataProvider provideOrderByWithExpectedOrder
     */
    public function testAddOrderByFallbackEnsuresThatOrderIsPreservedWhenAppendingIdToOrderby($orderBy, $order, $expected)
    {
        WpService::set(new FakeWpService(['addAction' => true]));
        $query = $this->getWpQuery();
        $query->set('orderby', $orderBy);
        $query->set('order', $order);
        $archive = new Archive();

        $archive->addOrderByFallback($query);

        $this->assertEquals($expected, $query->get('orderby'));
    }

    public function provideOrderByWithExpectedOrder()
    {
        return [
            'date ASC'                                    => ['date', 'ASC', ['date' => 'ASC', 'ID']],
            'date DESC'                                   => ['date', 'DESC', ['date' => 'DESC', 'ID']],
            'modified ASC'                                => ['modified', 'ASC', ['modified' => 'ASC', 'ID']],
            'modified DESC'                               => ['modified', 'DESC', ['modified' => 'DESC', 'ID']],
            'array with one value (date ASC)'             => [['date'], 'ASC', ['date' => 'ASC', 'ID']],
            'array with one value (date DESC)'            => [['date'], 'DESC', ['date' => 'DESC', 'ID']],
            'array with one value (modified ASC)'         => [['modified'], 'ASC', ['modified' => 'ASC', 'ID']],
            'array with one value (modified DESC)'        => [['modified'], 'DESC', ['modified' => 'DESC', 'ID']],
            'array with key and value (date => ASC)'      => [['date' => 'ASC'], 'DESC', ['date' => 'ASC', 'ID']],
            'array with key and value (date => DESC)'     => [['date' => 'DESC'], 'DESC', ['date' => 'DESC', 'ID']],
            'array with key and value (modified => ASC)'  => [['modified' => 'ASC'], 'ASC', ['modified' => 'ASC', 'ID']],
            'array with key and value (modified => DESC)' => [['modified' => 'DESC'], 'DESC', ['modified' => 'DESC', 'ID']],
        ];
    }

    /**
     * @testdox addOrderByFallback() does not append ID to orderby if orderby is not date or modified
     * @dataProvider provideOrderByMismatch
     */
    public function testAddOrderByFallbackDoesNotAppendIdToOrderbyIfOrderbyIsNotDateOrModified($orderBy)
    {
        WpService::set(new FakeWpService(['addAction' => true]));
        $query = $this->getWpQuery();
        $query->set('orderby', $orderBy);
        $archive = new Archive();

        $archive->addOrderByFallback($query);

        $this->assertEquals($orderBy, $query->get('orderby'));
    }

    public function provideOrderByMismatch()
    {
        return [
            'null'                                       => [null],
            'empty string'                               => [''],
            'array with multiple values'                 => [['date', 'modified']],
            'array with key and value (date => DESC)'    => [['date' => 'DESC', 'ID' => 'ASC']],
            'array with key and value (modified => ASC)' => [['modified' => 'ASC', 'ID' => 'DESC']],
        ];
    }

    /**
     * @testdox addOrderByFallback() does not append ID to orderby if query is not main query
     * @dataProvider provideOrderByMatch
     */
    public function testAddOrderByFallbackDoesNotAppendIdToOrderbyIfQueryIsNotMainQuery($orderBy)
    {
        WpService::set(new FakeWpService(['addAction' => true]));
        $query = $this->getWpQuery(false);
        $query->set('orderby', $orderBy);
        $archive = new Archive();

        $archive->addOrderByFallback($query);

        $this->assertEquals($orderBy, $query->get('orderby'));
    }

    private function getWpQuery(bool $isMainQuery = true)
    {
        return new class ($isMainQuery) {
            public array $vars = [];

            public function __construct(private bool $isMainQuery)
            {
            }

            public function is_main_query(): bool
            {
                return $this->isMainQuery;
            }

            public function get(string $key): mixed
            {
                return $this->vars[$key] ?? null;
            }

            public function set(string $key, mixed $value): void
            {
                $this->vars[$key] = $value;
            }
        };
    }
}
