<?php

namespace Zuki\ILS\Driver;

class MyLibrary implements \VuFind\ILS\Driver\DriverInterface
{
    /**
     * Driver configuration
     *
     * @var array
     */
    protected $config = [];

    /**
     * Connection used when getting random bib ids from Solr
     *
     * @var SearchService
     */
    protected $searchService;

    /**
     * Constructor
     *
     * @param \VuFind\Record\Loader $loader Record loader
     */
    public function __construct(\VuFindSearch\Service $service)
    {
        $this->searchService = $service;
    }

    /**
     * Set configuration.
     *
     * Set the configuration for the driver.
     *
     * @param array $config Configuration array (usually loaded from a VuFind .ini
     * file whose name corresponds with the driver class name).
     *
     * @return void
     */
    public function setConfig($config) {
        $this->config = $config;
    }

      /**
       * Initialize the driver.
       *
       * Validate configuration and perform all resource-intensive tasks needed to
       * make the driver active.
       *
       * @return void
       */
      public function init() {
          // No special processing needed here.
      }

    /**
     * Get Status
     *
     * This is responsible for retrieving the status information of a certain
     * record.
     *
     * @param string $id The record id to retrieve the holdings for
     *
     * @throws \VuFind\Exception\ILS
     * @return mixed     On success, an associative array with the following keys:
     * id, availability (boolean), status, location, reserve, callnumber.
     */
    public function getStatus($id) {
        $record = $this->searchService->retrieve('Solr', $id)->first();
        $callnumber = $volyear = '';
        $location = 'Unknown';

        if (null !== $record) {
            $callnumber = $record->getShelf();
            if (strlen($callnumber) > 1) {
                $shelf = substr($callnumber, 1, 1);
                if ($shelf == '2' or ($shelf >= 'A' and $shelf <= 'I')) {
                    $location = '2nd floor';
                } else {
                    $location = '1st floor';
                }
            }
            $volyear = $record->getVolYear();
        }

        $holding[] = array(
            'id'           => $id,
            'number'       => 1,
            'availability' => true,
            'status'       => 'Available',
            'location'     => $location,
            'reserve'      => false,
            'callnumber'   => $callnumber,
            'duedate'      => '',
            'summary'      => $volyear
            );
        return $holding;
    }

    /**
     * Get Statuses
     *
     * This is responsible for retrieving the status information for a
     * collection of records.
     *
     * @param array $ids The array of record ids to retrieve the status for
     *
     * @throws \VuFind\Exception\ILS
     * @return array     An array of getStatus() return values on success.
     */
    public function getStatuses($ids) {
        $status = array();
        foreach ($ids as $id) {
            $status[] = $this->getStatus($id);
        }
        return $status;
    }

    /**
     * Get Holding
     *
     * This is responsible for retrieving the holding information of a certain
     * record.
     *
     * @param string $id      The record id to retrieve the holdings for
     * @param array  $patron  Patron data
     * @param array  $options Extra options
     *
     * @throws \VuFind\Exception\ILS
     * @return array         On success, an associative array with the following
     * keys: id, availability (boolean), status, location, reserve, callnumber,
     * duedate, number, barcode.
     */
    public function getHolding($id, array $patron = null, array $options = []) {
        return $this->getStatus($id);
    }

    /**
     * Get Purchase History
     *
     * This is responsible for retrieving the acquisitions history data for the
     * specific record (usually recently received issues of a serial).
     *
     * @param string $id The record id to retrieve the info for
     *
     * @throws \VuFind\Exception\ILS
     * @return array     An array with the acquisitions data on success.
     */
    public function getPurchaseHistory($id) {
        return array();
    }

}

