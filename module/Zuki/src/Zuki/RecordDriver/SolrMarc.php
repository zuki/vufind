<?php

namespace Zuki\RecordDriver;

class SolrMarc extends \VuFind\RecordDriver\SolrMarc
{
    /**
     * Get Shelf for the record.
     *
     * @return String
     */
    public function getShelf()
    {
        return $this->fields['shelf'] ?? '';
    }

    /**
     * Get volyear for the record.
     *
     * @return String
     */
    public function getVolYear()
    {
        $vol = $this->fields['vol'] ?? '';
        $year = $this->fields['year'] ?? '';
        $volyear = $vol;
        if ('' !== $year) {
            $volyear .= '<'.$year.'>';
        }

        return $volyear;
    }


}

