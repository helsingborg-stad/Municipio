<?php

namespace Municipio\Controller\SingularEvent\EnsureVisitingSingularOccasion;

use DateTime;
use Municipio\Controller\SingularEvent\EnsureVisitingSingularOccasion\Redirect\RedirectInterface;
use Municipio\Controller\SingularEvent\Mappers\Occasion\OccasionInterface;
use WpService\Contracts\WpRedirect;

class EnsureVisitingSingularOccasion
{
    private array $occasions = [];

    public function __construct(private RedirectInterface $redirector, private ?DateTime $currentDate = null, OccasionInterface ...$occasions)
    {
        $this->occasions = $occasions;
    }

    /**
     * Ensure that we are visiting a singular occasion
     * If not, redirect to the first occasion in the nearest future
     * If no future occasion exists, redirect to the last past occasion
     */
    public function ensureVisitingSingularOccasion(): void
    {
        if (!empty($this->currentDate)) {
            return;
        }

        // Redirect to the first future occasion if it exists
        $futureOccasion = $this->findFirstFutureOccasion(...$this->occasions);
        if ($futureOccasion) {
            $this->redirector->redirect($futureOccasion->getUrl());
            return;
        }

        // If no future occasion exists, redirect to the last past occasion
        $lastPastOccasion = $this->findLastPastOccasion(...$this->occasions);
        if ($lastPastOccasion) {
            $this->redirector->redirect($lastPastOccasion->getUrl());
            return;
        }
    }

    private function findFirstFutureOccasion(OccasionInterface ...$occasions): ?OccasionInterface
    {
        foreach ($occasions as $occasion) {
            if (strtotime($occasion->getDateTime()) >= time()) {
                return $occasion;
            }
        }

        return null;
    }

    private function findLastPastOccasion(OccasionInterface ...$occasions): ?OccasionInterface
    {
        $reversedOccasions = array_reverse($occasions);
        foreach ($reversedOccasions as $occasion) {
            if (strtotime($occasion->getDateTime()) < time()) {
                return $occasion;
            }
        }

        return null;
    }
}
