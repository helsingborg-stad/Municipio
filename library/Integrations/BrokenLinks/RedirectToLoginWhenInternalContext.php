<?php

namespace Municipio\Integrations\BrokenLinks;

use Municipio\HooksRegistrar\Hookable;
use Municipio\Integrations\BrokenLinks\Config\BrokenLinksConfig;
use WpOrg\Requests\Exception\Http;
use WpService\WpService;

class RedirectToLoginWhenInternalContext implements Hookable
{
  public function __construct(private WpService $wpService, private BrokenLinksConfig $config){}

  public function addHooks() : void
  {
    $this->wpService->addAction('wp_head', [$this, 'redirectIfBrokenLink']);
  }

  /**
   * Redirects to login page if broken link is detected
   * 
   * @return void
   */
  public function redirectIfBrokenLink()
  {
    if($this->config->shouldRedirectToLoginPageWhenInternalContext()) {
      if(!$this->wpService->isUserLoggedIn()) {

        $currentUrl = $this->wpService->getPermalink(\Municipio\Helper\CurrentPostId::get());

        echo $this->render(
          $this->wpService->wpLoginUrl($currentUrl)
        );
      }
    }
  }

  /**
   * Adds JS to redirect to login page
   * 
   * @return void
   */
  public function render($loginUrl) {
    return sprintf(
        '<script>
            document.addEventListener("brokenLinkContextDetectionInternal", () => {
                window.location.href = "%s";
            });
        </script>',
        $loginUrl
    );
  }
}