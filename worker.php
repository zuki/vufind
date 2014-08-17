<?php
$worker = new GearmanWorker();
$worker->addServer("127.0.0.1", 4730);
$worker->addFunction('register_marc', 'registerMARC');
$worker->addFunction('index_marc', 'indexMARC');
$worker->addFunction('index_original', 'indexOriginal');
while ($worker->work());

function registerMARC($job) {
    $tag = $job->workload();
    list($id, $shelf, $isbn, $vols, $year) = explode(":", $tag);
    $result = getMarc($id);
    if (PEAR::isError($result)) {
        printf("Error occured when get MARC record: %s\n", $result->getMessage());
    } else {
        $marc_file = register($result, $shelf, $isbn, $vols, $year);
        import($marc_file, 'import-ndl.properties');
   }
}

function indexMARC($job) {
    import($job->workload());
}

function indexOriginal($job) {
    import($job->workload(), 'import-ndl.properties');
}

// Import MARC
function import($file, $properties='import.properties') {
    $propaties_path = '/home/dspace/vufind/local/import/' . $properties;
    exec("/home/dspace/vufind/import-marc.sh -p ${propaties_path} ${file} >> worker.log 2>&1", $output, $rcode);
    if ($rcode == 0) {
        preg_match("|/home/dspace/vufind/data/marc/(.*)\.mrc|", $file, $match);
        printf("Registered: %s\n", $match[1]);
    } else {
        printf("Error occured at importing the file: %s\n", $file);
    }
}
    
// Get a MARC record from NDL-OPAC
function getMarc($source) {
    require_once "HTTP/Client.php";

    $client = new HTTP_Client(); 
    try {
        // 1st Get: Get a record with the specified ID
        $url = 'http://id.ndl.go.jp/bib/' . $source;
        $client->get($url); 
        $response = $client->currentResponse(); 
        // 2nd Get: For SSO authentication, loop 1
        preg_match('/<a href=\"([^\"]+)\">/', $response['body'], $match);
        $url = $match[1];
        $client->get($url);
        $response = $client->currentResponse(); 
        // 3rd Get: For SSO authentication, loop 2
        preg_match("/var url = \'([^\']+)\'/", $response['body'], $match);
        $url = $match[1];
        $client->get($url);
        $response = $client->currentResponse(); 
        // 4th Get: Get its record display page
        preg_match('/<a href=\"([^\"]+)\">/', $response['body'], $match);
        $url = preg_replace('/&amp%3B/', '&', $match[1]);
        $client->get($url);
        $response = $client->currentResponse(); 
        // 5th Get: Get its download page
        preg_match('/<a href=\"([^\"]+)\"\s*title=\"選択したデータをパソコンにダウンロードする\"/', $response['body'], $match);
        $url = preg_replace('/&amp;/', '&', $match[1]);
        $client->get($url);
        $response = $client->currentResponse(); 
        // 1st Post: Get its marc record 
        preg_match('/<form method=POST\s*name=form1\s*action=\"([^\"]+)\".*name=doc_library value=\"([^\"]+)\".*doc_number value=\"([^\"]+)\"/s', $response['body'], $match);

        $params = 'func=full-mail&preview_required=Y&doc_library='.$match[2].'&doc_number='.$match[3].'&option_type=&encoding=NONE&format=997';
        $client->post($match[1], $params, true);
        $response = $client->currentResponse();

        return $response['body'];
    } catch (PEAR_Error $e) {
        return $e;
    }
}

// Register a MARC record with its holding information
function register($marc_string, $shelf, $isbn, $vols, $year) {
    require_once "File/MARC.php";

    $marc = new File_MARC($marc_string, File_MARC::SOURCE_STRING);
    $record = $marc->next();
    
    if (!empty($isbn)) {
	    $sbisbn[] = new File_MARC_Subfield('a', $isbn);
        $fisbn = new File_MARC_Data_Field('020', $sbisbn);
        $f015 = $record->getField('015');
        $record->insertField($fisbn, $f015);
		}
	
    $sbshelf[] = new File_MARC_Subfield('a', $shelf);
    $f852 = new File_MARC_Data_Field('852', $sbshelf);
	  $record->appendField($f852);

    if (!empty($vols) or !empty($year)) {
        if (!empty($vols)) {
            $volyear[] = new File_MARC_Subfield('a', $vols);
        }
        if (!empty($year)) {
            $volyear[] = new File_MARC_Subfield('i', $year);
        }
        $f963 = new File_MARC_Data_Field('963', $volyear, ' ', '0');
        $record->appendField($f963);
    }
    
    $id = $record->getField('001')->getData();
    $marc_file = '/home/dspace/vufind/data/marc/JP'.$id.'.mrc'; 
    $fh = fopen($marc_file, 'w');
    fwrite($fh, $record->toRaw());
    fclose($fh);
    
    chmod($marc_file, 0666);

    return $marc_file;
}


?>
