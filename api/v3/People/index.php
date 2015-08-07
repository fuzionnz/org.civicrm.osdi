<?php
require_once '/srv/www/buildkit/build/drupal-demo/sites/all/libraries/vendor/autoload.php';

use Nocarrier\Hal;

$json = file_get_contents('http://camus.fuzion.co.nz/sites/all/modules/civicrm/extern/rest.php?entity=People&action=get&json={"sequential":1}&options[limit]=0&&api_key=9BivcYv1cOT7md6Rxom8Stiz&key=gNhqb5uGUaiLAHrZ');

$json2 = file_get_contents('http://camus.fuzion.co.nz/sites/all/modules/civicrm/extern/rest.php?entity=DashboardContact&action=get&json={"sequential":1}&api_key=9BivcYv1cOT7md6Rxom8Stiz&key=gNhqb5uGUaiLAHrZ');

$json3 = file_get_contents('http://camus.fuzion.co.nz/sites/all/modules/civicrm/extern/rest.php?entity=Pledge&action=get&json={"sequential":1}&api_key=9BivcYv1cOT7md6Rxom8Stiz&key=gNhqb5uGUaiLAHrZ');

$array = json_decode($json, true);
$array2 = json_decode($json2, true);
$array3 = json_decode($json3, true);

$count=sizeof($array['values']);

$hal = new \Nocarrier\Hal('/sites/default/ext/org.civicrm.osdi/api/v3/People/', ['per_page' => $count,'page' => 1,'total_records' => $count]);

foreach($array['values'] as $key => $value){
    $i = $array['values'][$key]['contact_id'];
$resource = new \Nocarrier\Hal(
    '/People?contact_id='.$array['values'][$key]['contact_id'],
    array(
        'given_name' => $array['values'][$key]['given_name'],
        'family_name' => $array['values'][$key]['family_name'],
        'email_addresses' => array(
            'primary' => true,
            'address' => $array['values'][$key]['email']),
        'identifiers' => array('osdi-person-'.'['.$key.']'),
        'id'=> $array['values'][$key]['contact_id'],
        'created_date' => $array2['values'][$i]['created_date'],
        'modified_date' => date("Y/m/d"),
        'custom_fields' => array(
            'email' => $array['values'][$key]['email'],
            'full_name' => $array['values'][$key]['given_name'].' '.$array['values'][$key]['family_name'],
            'event_code' => 'xx',
            'address' => $array['values'][$key]['postal_addresses'],
            'zip' => $array['values'][$key]['zip_code'],
            'pledge' => $array3['values'][$i]['pledge_id']),
        'postal_addresses' => array(
            array(
            'address_lines' => array(null),
            'postal_code' => $array['values'][$key]['zip_code'],
            'address_status' => 'Verified/Not Verified',
            'primary' => 'True/False',)),
        'phone_numbers' => array(
            array(
            'number' => $array['values'][$key]['number'],)),
        '_embedded' => array(
            'osdi:tags' => array())
        )
    );


$resource->addLink('addresses', 'http://api.opensupporter.org/api/v1/people/X/addresses');
$resource->addLink('question_answers', 'http://api.opensupporter.org/api/v1/people/X/question_answers');
$resource->addLink('self', 'http://api.opensupporter.org/api/v1/people/X');
$resource->addLink('osdi-tags', 'http://api.opensupporter.org/api/v1/people/X/tags');

$hal->addResource('osdi-people', $resource);
}

echo $hal->asJson();
