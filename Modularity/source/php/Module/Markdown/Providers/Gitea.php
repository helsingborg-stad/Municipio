<?php 

namespace Modularity\Module\Markdown\Providers;

use Modularity\Module\Markdown\Providers\ProviderInterface;

class Gitea extends BaseProvider implements ProviderInterface
{
    public function isValidProviderUrl(string $url): bool
    {
        $pattern = '/gitea.io\/.+\/.+\/raw\/.+\/.+\.md$/';
        return (bool) preg_match($pattern, $url);
    }

    public function getExample(): string
    {
        return 'https://gitea.io/username/repository/raw/branch/README.md';
    }

    public function getName(): string
    {
        return 'Gitea';
    }
}