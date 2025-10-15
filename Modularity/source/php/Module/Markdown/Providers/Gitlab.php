<?php 

namespace Modularity\Module\Markdown\Providers;

use Modularity\Module\Markdown\Providers\ProviderInterface;

class Gitlab extends BaseProvider implements ProviderInterface
{
    public function isValidProviderUrl(string $url): bool
    {
        $pattern = '/gitlab.com\/.+\/.+\/-\/raw\/.+\.md$/';
        if (!preg_match($pattern, $url)) {
            return false;
        }
        return true;
    }

    public function getExample(): string
    {
        return 'https://gitlab.com/username/repository/-/raw/branch/path/to/file.md';
    }

    public function getName(): string
    {
        return 'GitLab';
    }
}