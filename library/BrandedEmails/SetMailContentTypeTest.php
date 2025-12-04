<?php

namespace Municipio\BrandedEmails;

use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

class SetMailContentTypeTest extends TestCase
{
    public function testGetContentType()
    {
        $setMailContentType = new SetMailContentType('text/html', $this->getWpService());

        $this->assertEquals('text/html', $setMailContentType->getContentType());
    }

    public function testAddHooks()
    {
        $wpService          = $this->getWpService();
        $setMailContentType = new SetMailContentType('text/html', $wpService);

        $setMailContentType->addHooks();

        $this->assertEquals('wp_mail_content_type', $wpService->filtersAdded[0][0]);
        $this->assertTrue(method_exists($setMailContentType, $wpService->filtersAdded[0][1][1]));
    }

    private function getWpService(): AddFilter
    {
        return new class implements AddFilter {
            public array $filtersAdded = [];
            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->filtersAdded[] = [ $hookName, $callback, $priority, $acceptedArgs];
                return true;
            }
        };
    }
}
