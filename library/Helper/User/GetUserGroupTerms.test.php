<?php

namespace Municipio\Helper\User;

use WpService\Implementations\FakeWpService;
use Municipio\Helper\SiteSwitcher\SiteSwitcher;
use WpService\Contracts\GetMainSiteId;
use Municipio\Helper\User\GetUserGroupTerms;
use PHPUnit\Framework\TestCase;

/**
 * Class GetUserGroupTerms
 *
 * This class is responsible for retrieving the group terms associated with a user.
 */
class GetUserGroupTermsTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $getUserGroupTerms = new GetUserGroupTerms(
            new FakeWpService(),
            'user-group',
            $this->createStub(SiteSwitcher::class)
        );

        $this->assertInstanceOf(GetUserGroupTerms::class, $getUserGroupTerms);
    }

    /**
     * @testdox get() returns user group terms
     */
    public function testGetReturnsUserGroupTerms()
    {

        $getUserGroupTerms = new GetUserGroupTerms(
            new FakeWpService(
                [
                    'getTerms' => function () {
                        return [
                            (object) ['term_id' => 1, 'name' => 'Group 1'],
                            (object) ['term_id' => 2, 'name' => 'Group 2'],
                        ];
                    },
                    'isMultisite' => function () {
                        return false;
                    },
                    'isMainSite' => function () {
                        return false;
                    },
                ]
            ),
            'user-group',
            $this->createStub(SiteSwitcher::class)
        );

        $this->assertEquals([
            (object) ['term_id' => 1, 'name' => 'Group 1'],
            (object) ['term_id' => 2, 'name' => 'Group 2'],
        ], $getUserGroupTerms->get());
    }
}
