<?php

namespace Zuki\Controller;

use HTTP_Request2;
use File_MARC;
use PEAR;

use VuFindSearch\Query\Query;
use VuFindSearch\Query\QueryGroup;
use VuFindSearch\Backend\Exception\BackendException;

class AdminController extends \VuFindAdmin\Controller\AdminController
{
    /**
     * Records maintenance
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function recordsAction()
    {
        $util = $this->params()->fromQuery('util');
        $id = $this->params()->fromQuery('id');
        if (isset($util)) {
            if ($id) {
                return $this->forwardTo('Admin', $util);
            } else {
                $this->flashMessenger()->setNamespace('error')
                    ->addMessage($this->translate(
                        'Please specify a record ID'
                    ));
            }
        }
        return $this->createViewModel();
    }

    /**
     * レコード管理:  閲覧
     *
     * @return mixed
     */
    public function viewrecordAction()
    {
        $id = $this->params()->fromQuery('id');
        $first = $this->serviceLocator->get(\VuFindSearch\Service::class)->retrieve('Solr', $id)->first();

        if (null === $first) {
            $this->flashMessenger()->setNamespace('error')
                ->addMessage($this->translate(
                    'The specified record does not exist.'
                ));
            $this->getRequest()->setQuery(new \Zend\Stdlib\Parameters());
            return $this->forwardTo('Admin', 'Records');
        }

        $view = $this->createViewModel();
        $view->record = $first->getRawData();
        $view->recordId = $id;
        return $view;
    }

    /**
     * Add record from NDL Opac
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addrecordAction()
    {
        return $this->createViewModel();
    }

    /**
     * Search NDLSearch or LC.
     *
     * @return void
     * @access public
     */
    public function searchrecordAction()
    {
        $source = $this->params()->fromQuery('source');
        $field  = $this->params()->fromQuery('field');
        $value  = $this->params()->fromQuery('value');

        $forward  = true;
        $ns      = null;
        $message = null;
        try
        {
            if (!$value) {
               $ns = 'error';
               $message = '検索値を入力してください。';
            } elseif ($source != 'ndl' && $source != 'lc') {
               $ns = 'error';
               $message = sprintf('検索元コードが違います: %s', $source);
            } else if ($source == 'ndl') {
                $records = $this->getRecordsFromNDL(
                    $this->serviceLocator->get(\VuFindSearch\Service::class), $field, $value);
                if (!$records) {
                    $ns = 'info';
                    $message = sprintf('該当レコードなし: %s=%s', $field, $value);
                } else {
                    $foward = false;
                }
            } else {
                $records = $this->getRecordsFromLC(
                    $this->serviceLocator->get(\VuFindSearch\Service::class), $field, $value);
                if (!$records) {
                    $ns = 'info';
                    $message = sprintf('該当レコードなし: %s=%s', $field, $value);
                } else {
                    $foward = false;
                }
            }
        } catch (BackendException $e) {
            $forward = true;
            $ns = 'error';
            $message = sprintf('%s 検索中にエラーが発生しました: %s',
                $source, $e->getMessage());
        }

        if ($forward) {
            $this->flashMessenger()->setNamespace($ns)->addMessage($message);
            $this->getRequest()->setQuery(new \Zend\Stdlib\Parameters());
            $this->forwardTo('Admin', 'AddRecord');
        }

        $view = $this->createViewModel();
        $view->setTemplate('admin/addrecord');
        $view->records = $records;
        $view->source = $source;
        return $view;
    }

    /**
     * Register Marc record.
     *
     * @return void
     * @access public
     */
    public function registerrecordAction()
    {
        $selected = $this->params()->fromQuery('selected');

        if (empty($selected)) {
            $this->getRequest()->setQuery(new \Zend\Stdlib\Parameters());
            return $this->forwardTo('Admin', 'AddRecord');
        }

        $shelf = $this->params()->fromQuery('shelf');
        $isbn = $this->params()->fromQuery('isbn');
        $vols = $this->params()->fromQuery('vols');
        $year = $this->params()->fromQuery('year');
        $source = $this->params()->fromQuery('source');

        if (empty($shelf)) {
                $this->flashMessenger()->setNamespace('error')
                    ->addMessage($this->translate(
                        '書架番号を入力してください。'));
        } elseif ($source == 'ndl') {
            $marc_string = $this->getJPMarc($selected);
            if (PEAR::isError($marc_string)) {
                $this->flashMessenger()->setNamespace('error')
                    ->addMessage(sprintf('%s: JPMmarcの取得中にエラーが発生しました: %s',
                    $selected, $marc_string->getMessage()));
                $this->getRequest()->setQuery(new \Zend\Stdlib\Parameters());
                $this->forwardTo('Admin', 'AddRecord');
            }

            $marc_path = $this->updateJPMarc($marc_string, $shelf, $isbn, $vols, $year);
            $propaties_path = '/usr/local/vufind/local/import/import.properties';
            exec("/usr/local/vufind/import-marc.sh -p ${propaties_path} ${marc_path} >> worker.log 2>&1", $output, $rcode);
            if ($rcode == 0) {
                preg_match("|/usr/local/vufind/data/marc/(.*)\.mrc|", $marc_path, $match);
                $this->flashMessenger()->setNamespace('info')
                    ->addMessage(sprintf('正常に登録されました: id = %s', $match[1]));
            } else {
                $this->flashMessenger()->setNamespace('error')
                    ->addMessage(sprintf('登録中にエラーが発生しました: jnb = %s, shelf = %s, isbn = %s', $selected, $shelf, $isbn));
            }
        } elseif ($source == 'lc') {
            try
            {
                $record = $this->retrieveFromLC(
                    $this->getServiceLocator()->get(\VuFind\Search::class), $selected, $shelf, $isbn, $vols, $year);
                $marc_file = 'LC'.$selected.'.mrc';
                $marc_path = $this->getServiceLocator()->get(\VuFind\Config::class)->get('config')->MARC->Directory.$marc_id;
                $fh = fopen($marc_path, 'w');
                fwrite($fh, $record->toRaw());
                fclose($fh);
                chmod($marc_path, 0666);

                $propaties_path = '/usr/local/vufind/local/import/import_usmarc.properties';
                exec("/usr/local/vufind/import-marc.sh -p ${propaties_path} ${marc_path} >> worker.log 2>&1", $output, $rcode);
                if ($rcode == 0) {
                    $this->flashMessenger()->setNamespace('info')
                        ->addMessage(sprintf('正常に登録されました: id = %s', $marc_file));
                } else {
                    $this->flashMessenger()->setNamespace('error')
                        ->addMessage(sprintf('登録中にエラーが発生しました: jnb = %s, shelf = %s, isbn = %s', $selected, $shelf, $isbn));
                }
            } catch (BackendException $e) {
                $this->flashMessenger()->setNamespace('error')
                    ->addMessage(sprintf('処理中にエラーが発生しました: id = %s, shelf = %s, isbn = %s', $selected, $shelf, $isbn));
            }
        }

        $this->getRequest()->setQuery(new \Zend\Stdlib\Parameters());
        return $this->forwardTo('Admin', 'AddRecord');
    }

    /**
     * 国会図書館サーチAPIを使用し、ISBN, タイトルなどから全国書誌番号を得る
     *
     * @param \VuFindSearch\Service a search service
     * @param String a search field
     * @param String a search value
     * @return [String] [全国書誌番号、書誌データ]の配列
     * @access private
     */
    private function getRecordsFromNDL($service, $field, $value)
    {
        $queries = array();
        $queries[] = new Query($value, $field);
        $queries[] = new Query('iss-ndl-opac','dpid');
        $query = new QueryGroup('AND', $queries);

        $collection = $service->search('Ndl', $query, 1, 20);

        if ($collection->getTotal() == 0) {
            return null;
        }
        $records = array();
        foreach ($collection->getRecords() as $record) {
            //$id = $record->getNDLOpacID();
            $id = $record->getJPNO();
            if ($id === null) {
                continue;
            }
            // $record: NDL Record Driver object
            $citation = $record->getTitle();
            if ($record->getPrimaryAuthor()) {
                $citation .=  ' / ' . $record->getPrimaryAuthor();
            }
            if ($record->getVolume()) {
                $citation .=  ', ' . $record->getVolume();
            }
            if ($record->getPubDate()) {
                $citation .=  ' (' . $record->getPubDate() . ')';
            }
            if ($record->getSeriesTitle()) {
                $citation .=  ' -- (' . $record->getSeriesTitle() . ')';
            }
            $records[] = array($id, $citation);
        }

        return $records;
    }

    /**
     * NDL-BIbを全国書誌番号で検索しJPMarcを得る（スクレイピング）
     *
     * @param String 全国書誌番号
     * @param String a search value
     * @return mixed (String|PEAR_Error) JPMArc か エラー
     * @access private
     */
    private function getJPMarc($jnb) {
        try {
            // 1st Get: 全国書誌番号で検索
            $url = 'https://ndl-bib.ndl.go.jp/F/?func=find-a&find_code=JNB&request='.$jnb;
            $req1 = new \HTTP_Request2($url);
            $res1 = $req1->send();
            //2nd Get: ダウンロードページをゲット
            preg_match('/<a href=\"([^\"]+)\"\s*title=\"選択したデータをパソコンにダウンロードする\"/', $res1->getBody(), $match);
            $url = preg_replace('/&amp;/', '&', $match[1]);
            $req2 = new \HTTP_Request2($url);
            $res2 = $req2->send();
            // 1st Post: JPMarcをゲット
            preg_match('/<form method=POST\s*name=form1\s*action=\"([^\"]+)\".*name=doc_library value=\"([^\"]+)\".*doc_number value=\"([^\"]+)\"/s', $res2->getBody(), $match);
            $query3 = [
              'func' => 'full-mail',
              'preview_required' => 'Y',
              'doc_library' => $match[2],
              'doc_number' => $match[3],
              'option_type'=> '',
              'encoding' => 'NONE',
              'format' => '997'
            ];
            $req3 = new \HTTP_Request2($match[1], HTTP_Request2::METHOD_POST);
            $req3->addPostParameter($query3);
            $res3 = $req3->send();
            return $res3->getBody();
        } catch (PEAR_Error $e) {
            return $e;
        }
    }

    /**
     * 追加データをJPMarcに組込み、ファイルに書き込む
     *
     * @param String $marc_string JPMarc文字列
     * @param String $shelf 初夏番号
     * @param String $isbn ISBN
     * @param String $vols 巻号
     * @param String $year 出版年
     * @return String JPMarcファイルパス
     * @access private
     */
    private function updateJPMarc($marc_string, $shelf, $isbn, $vols, $year) {
        $marc = new \File_MARC($marc_string, File_MARC::SOURCE_STRING);
        $record = $marc->next();

        if (!empty($isbn)) {
    	      $sbisbn[] = new \File_MARC_Subfield('a', $isbn);
            $fisbn = new \File_MARC_Data_Field('020', $sbisbn);
            $f015 = $record->getField('015');
            $record->insertField($fisbn, $f015);
    		}

        $sbshelf[] = new \File_MARC_Subfield('a', $shelf);
        $f852 = new \File_MARC_Data_Field('852', $sbshelf);
    	  $record->appendField($f852);

        if (!empty($vols) or !empty($year)) {
            if (!empty($vols)) {
                $volyear[] = new \File_MARC_Subfield('a', $vols);
            }
            if (!empty($year)) {
                $volyear[] = new \File_MARC_Subfield('i', $year);
            }
            $f963 = new \File_MARC_Data_Field('963', $volyear, ' ', '0');
            $record->appendField($f963);
        }

        $id = $record->getField('001')->getData();
        $marc_path = '/usr/local/vufind/data/marc/JP'.$id.'.mrc';
        $fh = fopen($marc_path, 'w');
        fwrite($fh, $record->toRaw());
        fclose($fh);
        chmod($marc_path, 0666);

        return $marc_path;
    }

    private function getRecordsFromLC($service, $field, $value)
    {
        if ("rec.id" !== $field) {
            $field = 'bath.' . $field;
        }
        $query = new Query($value, $field);

        $collection = $service->search('Loc', $query, 1, 20);

        if ($collection->getTotal() == 0) {
            return null;
        }
        $records = array();
        foreach ($collection->getRecords() as $record) {
            $id       = $record->getUniqueID();
            $date     = $record->getPubYear();
            $title    = $record->getTitleStatement();
            $edition  = $record->getEdition();
            $series   = $record->getSeries();

            $citation = $title;
            if ($edition) {
                $citation .= '. ' . $edition;
            }
            $citation .= ' (' . $date . ')';
            if ($series) {
                $citation .= ' -- ( ' . $series[0]['name'];
                if ($series[0]['number']) {
                    $citation .= ' ' . $series[0]['number'];
                }
                $citation .= ')';
            }
            $records[] = array($id, $citation);
        }

        return $records;
    }

}

