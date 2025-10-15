<?php 

namespace Modularity\Module\Markdown\Providers;

use Modularity\Module\Markdown\Providers\ProviderInterface;

class Bitbucket extends BaseProvider implements ProviderInterface
{
    public function isValidProviderUrl(string $url): bool
    {
        $pattern = '/bitbucket.org\/.+\/.+\/raw\/.+\.md$/';
        if (!preg_match($pattern, $url)) {
            return false;
        }
        return true;
    }

    public function getExample(): string
    {
        return 'https://bitbucket.org/username/repository/raw/branch/path/to/file.md';
    }

    public function getName(): string
    {
        return 'Bitbucket';
    }
}