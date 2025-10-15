<?php 

namespace Modularity\Module\Markdown\Providers;

use Modularity\Module\Markdown\Providers\ProviderInterface;

class SourceHut extends BaseProvider implements ProviderInterface
{
    public function isValidProviderUrl(string $url): bool
    {
        $pattern = '/git.sr.ht\/~.+\/.+\/blob\/.+\/.+\.md$/';
        return (bool) preg_match($pattern, $url);
    }

    public function getExample(): string
    {
        return 'https://git.sr.ht/~username/repository/blob/branch/README.md';
    }

    public function getName(): string
    {
        return 'SourceHut';
    }
}