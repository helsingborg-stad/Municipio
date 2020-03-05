<?php

namespace BladeComponentLibrary\Component\Grid;

class Grid extends \BladeComponentLibrary\Component\BaseController  
{
    public function init() {
        //Extract array for eazy access (fetch only)
        extract($this->data);

        if ($container) {
            $this->setContainer($columns, $min_width, $max_width);
        }

        if (isset($col)) {
            $this->setCols($col);
        }

        if (isset($row)) {
            $this->setRows($row);
        }        
    }

    public function setContainer($columns, $min_width, $max_width)
    {
        if ($columns) {
            $this->data['attributeList']['style'] =
                "grid-template-columns: repeat(".$columns.
                ", minmax(".$min_width.", ".$max_width."));";
        }

        $this->data['classList'][] =
            $this->getBaseClass() . '__container';
    }

    public function setGaps($col_gap, $row_gap)
    {
        $this->data['classList'][] =
            $this->getBaseClass() . '__container__gap__col--' . $col_gap;

        $this->data['classList'][] =
            $this->getBaseClass() . '__container__gap__row--' . $row_gap;
        return;
    }

    public function setCols($col)
    {
        foreach ($col as $bp => $value) {
            $this->data['classList'][] =
                $this->getBaseClass() . '__column__start--' . strval($value[0]) . '@' . $bp;

            $this->data['classList'][] =
                $this->getBaseClass() . '__column__end--' . strval($value[1]) . "@" . $bp;
        }
        
    }

    public function setRows($row)
    {
        $aliases = [
            'start' => 0,
            'end' => 1
        ];

        foreach ($row as $bp => $line) {
            foreach ($aliases as $name => $key) {
                if ($line[$key] > 13) {
                    $this->data['attributeList']['style'] = 'grid-row-'. $name .': '. strval($line[$key]) .';';
                    return;
                }
            }

            $this->data['classList'][] =
                $this->getBaseClass() . '__row__start--' . strval($line[0]) . '@' . $bp;

            $this->data['classList'][] =
                $this->getBaseClass() . '__row__end--' . strval($line[1]) . "@" . $bp;
        }
    }
}