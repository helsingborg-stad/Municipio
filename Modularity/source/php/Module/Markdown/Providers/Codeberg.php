<?php 

namespace Modularity\Module\Markdown\Providers;

use Modularity\Module\Markdown\Providers\ProviderInterface;

class Codeberg extends BaseProvider implements ProviderInterface
{
    public function isValidProviderUrl(string $url): bool
    {
        $pattern = '/codeberg.org\/.+\/.+\/raw\/.+\/.+\.md$/';
        return (bool) preg_match($pattern, $url);
    }

    public function getExample(): string
    {
        return 'https://codeberg.org/username/repository/raw/branch/README.md';
    }

    public function getName(): string
    {
        return 'Codeberg';
    }
}