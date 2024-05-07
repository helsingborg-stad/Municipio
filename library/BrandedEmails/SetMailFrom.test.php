<?php

namespace Municipio\BrandedEmails;

use Municipio\BrandedEmails\Config\GetMailFrom;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

class SetMailFromTest extends TestCase
{
    /**
     * @testdox getMailFrom() returns the email from the config service if it is set
     */
    public function testGetMailFrom()
    {
        $setMailFrom = new SetMailFrom($this->getConfigService('foo@bar.baz'), $this->getWpService());
        $this->assertEquals('foo@bar.baz', $setMailFrom->getMailFrom('name@domain.name'));
    }

    /**
     * @testdox getMailFrom() returns the the default email if the config service returns null
     */
    public function testGetMailFromReturnsDefault()
    {
        $setMailFrom = new SetMailFrom($this->getConfigService(), $this->getWpService());
        $this->assertEquals('default@email.address', $setMailFrom->getMailFrom('default@email.address'));
    }

    /**
     * @testdox addHooks() is adding the filter to the wp_mail_from hook with a valid callback
     */
    public function testAddHooks()
    {
        $wpService   = $this->getWpService();
        $setMailFrom = new SetMailFrom($this->getConfigService(), $wpService);

        $setMailFrom->addHooks();

        $this->assertEquals('wp_mail_from', $wpService->filtersAdded[0][0]);
        $this->assertTrue(method_exists($setMailFrom, $wpService->filtersAdded[0][1][1]));
    }

    private function getConfigService(?string $email = null): GetMailFrom
    {
        return new class ($email) implements GetMailFrom {
            public function __construct(private ?string $email)
            {
            }

            public function getMailFrom(): ?string
            {
                return $this->email;
            }
        };
    }

    private function getWpService(): AddFilter
    {
        return new class implements AddFilter {
            public array $filtersAdded = [];
            public function addFilter(
                string $tag,
                callable $functionToAdd,
                int $priority = 10,
                int $acceptedArgs = 1
            ): bool {
                $this->filtersAdded[] = [ $tag, $functionToAdd, $priority, $acceptedArgs];
                return true;
            }
        };
    }
}
