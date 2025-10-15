<?php 

namespace Modularity\Module\Markdown\Providers;

use Modularity\Module\Markdown\Providers\ProviderInterface;

class CGit extends BaseProvider implements ProviderInterface
{
    public function isValidProviderUrl(string $url): bool
    {
        $pattern = '/\/plain\/.+\.md\?h=.+$/';
        return (bool) preg_match($pattern, $url);
    }

    public function getExample(): string
    {
        return 'https://git.kernel.org/pub/scm/linux/kernel/git/torvalds/linux.git/plain/README.md?h=master';
    }

    public function getName(): string
    {
        return 'CGit';
    }
}