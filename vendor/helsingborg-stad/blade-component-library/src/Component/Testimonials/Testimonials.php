<?php

namespace BladeComponentLibrary\Component\Testimonials;

/**
 * Class Testimonials
 * @package BladeComponentLibrary\Component\Testimonials
 */
class Testimonials extends \BladeComponentLibrary\Component\BaseController
{
    public function init()
    {
        //Extract array for eazy access (fetch only)
        extract($this->data);

        $this->compParams = [
            'testimonials' => $testimonials,
            'perRow' => $perRow,
            'componentElement' => $componentElement
        ];

        $this->mapData();
    }

    /**
     * Mapping data
     */
    public function mapData()
    {
        $this->data['testimonials'] = !empty($this->compParams['testimonials']) &&
        is_array($this->compParams['testimonials']) ? $this->compParams['testimonials'] : array();

        // Sanitize testimonials data
        $this->data['testimonials'] = array_map(
            function ($testimonial) {
                return array(
                    'name' => $testimonial['name'] ?? '',
                    'title' => $testimonial['title'] ?? '',
                    'testimonial' => $testimonial['testimonial'] ?? '',
                    'titleElement' => $testimonial['titleElement'] ?? 'h4',
                    'nameElement' => $testimonial['nameElement'] ?? 'h2',
                    'image' => $testimonial['image'] ?? '',
                    'avatar' => $testimonial['avatar'] ?? true,
                    'quoteColor' => $testimonial['quoteColor'] ?? 'grey',
                    'imageTop' => $testimonial['imageTop'] ?? false
                );
            },
            $this->compParams['testimonials']
        );

        $grid = $this->calculateGrid();
        $this->data['componentElement'] = ($this->compParams['componentElement']) ?
            $this->compParams['componentElement'] : 'div';
        
        $this->data['gridClasses'] = 'grid-xs-12 grid-sm-6 grid-lg-' . $grid;
    }

    /**
     * Calculate grid rows
     */
    public function calculateGrid()
    {
        $perRow = ((int)$this->compParams['perRow'] > 0 && (int)$this->compParams['perRow'] <= 12) ?
            (int)$this->compParams['perRow'] : 1;
        return is_int(12 / $perRow) ? 12 / $perRow : 12;
    }

}
