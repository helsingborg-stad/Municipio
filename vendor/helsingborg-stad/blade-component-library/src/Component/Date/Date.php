<?php

namespace BladeComponentLibrary\Component\Date;

/**
 * Class Dropdown
 * @package BladeComponentLibrary\Component\Date
 */
class Date extends \BladeComponentLibrary\Component\BaseController
{

    public function init() {
        //Extract array for eazy access (fetch only)
        extract($this->data);

        if ($action == "formatDate") {
            $this->data['refinedDate'] = $this->formatDate(strtotime($timestamp));
        } else if ($action == "timesince") {
            $this->data['refinedDate'] = $this->convertToHumanReadableUnit(strtotime($timestamp), true);
        } else if ($action == "timeuntil") {
            $this->data['refinedDate'] = $this->convertToHumanReadableUnit(strtotime($timestamp));
        }
    }

    private function formatDate($timestamp) {
        $format = 'D d M Y';
        return date ($format, $timestamp );
    }

    /**
	 * Get time since a specified date
     * @param Date A timestamp of which date to count since
     * @return String Time since in words
	 */
    private function convertToHumanReadableUnit($time, $timeSince = false) {
        $time = $timeSince ? time() - $time : $time - time();
        $time = ($time < 1) ? 1 : $time;
        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($tokens as $unit => $text) {
            $numberOfUnits = floor($time / $unit);

            if ($time > $unit) return $numberOfUnits.' '.$text.(($numberOfUnits>1) ? 's' : '');
        }

    }
}