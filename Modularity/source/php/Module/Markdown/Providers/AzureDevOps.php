<?php 

namespace Modularity\Module\Markdown\Providers;

use Modularity\Module\Markdown\Providers\ProviderInterface;

class AzureDevOps extends BaseProvider implements ProviderInterface 
{
    public function isValidProviderUrl(string $url): bool
    {
        $pattern = '/dev.azure.com\/.+\/.+\/_apis\/git\/repositories\/.+\/items\?.*path=.*\.md(&|$)/';
        return (bool) preg_match($pattern, $url);
    }

    public function getExample(): string
    {
        return 'https://dev.azure.com/organization/project/_apis/git/repositories/repo/items?path=/README.md&download=true';
    }

    public function getName(): string
    {
        return 'Azure DevOps';
    }
}