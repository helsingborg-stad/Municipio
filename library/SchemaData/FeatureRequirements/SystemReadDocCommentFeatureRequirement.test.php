<?php

namespace Municipio\SchemaData\FeatureRequirements;

use Municipio\IniService\IniServiceInterface;
use PHPUnit\Framework\TestCase;

class SystemReadDocCommentFeatureRequirementTest extends TestCase
{
    /**
     * Returns true if OPcache does not prevent reading comments.
     * @testWith ["1"]
     *           ["On"]
     */
    public function testReturnsTrueIfCanReadComments($iniValue)
    {
        $iniService = $this->getIniService();
        $iniService->set('opcache.enable', "1");
        $iniService->set('opcache.save_comments', $iniValue);
        $requirement = new SystemReadDocCommentFeatureRequirement($iniService);

        $this->assertTrue($requirement->isMet());
    }

    /**
     * Returns false if OPcache prevents reading comments.
     * @testWith ["Off"]
     *           ["0"]
     */
    public function testReturnsFalseIfCannotReadComments($iniValue)
    {
        $iniService = $this->getIniService();
        $iniService->set('opcache.enable', "1");
        $iniService->set('opcache.save_comments', $iniValue);
        $requirement = new SystemReadDocCommentFeatureRequirement($iniService);

        $this->assertFalse($requirement->isMet());
    }

    /**
     * Returns true if OPcache is not enabled.
     * @testWith ["Off"]
     *           ["0"]
     *           [""]
     */
    public function testReturnsFalseIfCacheNotEnabled($iniValue)
    {
        $iniService = $this->getIniService();
        $iniService->set('opcache.enable', $iniValue);
        $requirement = new SystemReadDocCommentFeatureRequirement($iniService);

        $this->assertTrue($requirement->isMet());
    }

    private function getIniService(): IniServiceInterface
    {
        return new class implements IniServiceInterface {
            private array $iniValues = [];

            public function set(string $key, string $value): void
            {
                $this->iniValues[$key] = $value;
            }

            public function get(string $key): string
            {
                return $this->iniValues[$key] ?? '';
            }
        };
    }
}
