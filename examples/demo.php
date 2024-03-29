<?php

// ini_set('display_errors',1) ;
// error_reporting(E_ALL) ;

// Include Composer autoload
require __DIR__."/../../../autoload.php";
require __DIR__."/../config.inc.php";

// Create the client
$client = new \Apidae\ApiClient\Client([
    'apiKey'        => 'XXX',
    'projectId'     => 000,
    'baseUrl'       => 'https://api.apidae-tourisme.com/',
    'apiKey'        => $config['apiKey'],
    'projectId'     => $config['projectId'],
    'baseUrl'       => $config['baseUrl']
]);

try {
    /*
     * Reference
     */
    $cities = $client->getReferenceCity(['query' => [ 'codesInsee' => ["38534", "69388", "74140"] ]]);
    var_dump(count($cities));

    $elements = $client->getReferenceElement(['query' => [ "elementReferenceIds" => [2, 118, 2338] ]]);
    var_dump(count($elements));

    $elements = $client->getReferenceInternalCriteria(['query' => [ "critereInterneIds" => [ 1068, 2168 ] ]]);
    var_dump(count($elements));

    $elements = $client->getReferenceSelection(['query' => [ "selectionIds" => [  64, 5896 ] ]]);
    var_dump(count($elements));


    /*
     * Object API's
     */
    $search = $client->searchObject(['query' => [ "searchQuery" => "vélo" ]]);
    var_dump($search['numFound']);

    $search = $client->searchObjectIdentifier(['query' => [ "searchQuery" => "vélo" ]]);
    var_dump($search['numFound']);

    $search = $client->searchAgendaIdentifier(['query' => [ "searchQuery" => "vélo" ]]);
    var_dump($search['numFound']);
    $search = $client->searchAgenda(['query' => [ "searchQuery" => "vélo" ]]);
    var_dump($search['numFound']);

    $search = $client->searchDetailedAgendaIdentifier(['query' => [ "searchQuery" => "vélo" ]]);
    var_dump($search['numFound']);

    $search = $client->searchDetailedAgenda(['query' => [ "searchQuery" => "vélo" ]]);
    var_dump($search['numFound']);

    $object = $client->getObjectById(['id' => 163512]);
    var_dump($object['id']);

    $object = $client->getObjectByIdentifier(['identifier' => 'sitraSKI275809']);
    var_dump($object['id']);
} catch (\Apidae\ApiClient\Exception\ApidaeException $e) {
    echo $e->getMessage();
    echo "\n";
    echo $e->getPrevious()->getMessage();
}
