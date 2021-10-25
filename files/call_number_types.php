<?php

require_once 'config.php';
require_once 'folio_api.php';



### Main ###

$cacheObj = null;


// If token not set in config file, get cached or new token.
if ( empty($folio_okapi_token) ) {
  $cacheObj = get_folio_okapi_token();
  $folio_okapi_token = $cacheObj->token;
}


// Request call number types and decode response.
$response = get_call_number_types($folio_okapi_token);

$responseObj = json_decode($response['body']);

$callNumberTypes = $responseObj->callNumberTypes;


// Build array of FOLIO call number type UUIDs assigned in config file with
// with value of corresponding SpineOMatic call number type.
$assigned_som_types = array();

foreach ($folio_call_number_type_uuids as $som_type => $uuids) {
  foreach ($uuids as $uuid) {
    $assigned_som_types[$uuid] = $som_type;
  }
}


// Initialize array with readable SpineOMatic call number type names.
$som_type_names = array(
    'Library of Congress and LC Children\'s Literature',
    'Dewey Decimal',
    'National Library of Medicine (NLM)',
    'Superintendent of Documents (SuDoc)',
    'SpineOMatic User-Defined Scheme',
);



### Functions ###

function get_call_number_types($folio_okapi_token) {

  global $folio_okapi_tenant;


  // Call FOLIO /call-number-types API for call number type UUIDs and names.
  $request_headers = array(
      "X-Okapi-Tenant: {$folio_okapi_tenant}",
      "X-Okapi-Token: {$folio_okapi_token}",
  );

  $curl_options = array(
      CURLOPT_HTTPGET => true,
      CURLOPT_HTTPHEADER => $request_headers,
  );

  $response = call_folio_api('/call-number-types?limit=1000', $curl_options);

  switch ($response['status_code']) {
    case 400:
      $response['error'] = 'FOLIO ERROR: Bad request';
      break;
    case 401:
      $response['error'] = 'FOLIO ERROR: Not authorized to perform requested action';
      break;
    case 500:
      $response['error'] = 'FOLIO ERROR: Internal server error';
      break;
  }

  return $response;
}



### Page ###
?>
<!DOCTYPE html>
<html>
  <head>
    <title>FOLIO Call Number Types</title>

    <style>
      body {
        text-align: center;
        font-family: sans-serif;
      }

      div#main {
        text-align: left;
      }

      div.error {
        border: 1px solid crimson;
        border-radius: 4px;
        padding: .5em;
        background-color: pink;
        color: crimson;
      }

      div#info {
        border-radius: 5px;
        padding: 1em;
        background-color: lightskyblue;
        color: navy;
      }

      table {
        width: 100%;
        border-collapse: collapse;
      }

      th {
        padding: .4em;
        text-align: center;
        font-weight: bold;
        background-color: grey;
      }

      td {
        padding: .4em;
      }

      tbody tr:nth-child(odd) {
        background-color: #fff;
      }

      tbody tr:nth-child(even) {
        background-color: #ddd;
      }

      tbody tr:hover {
        background-color: lightgreen;
      }

      tfoot td {
        font-weight: bold;
        background-color: grey;
      }
    </style>
  </head>
  <body>
    <div id="main">
<?php if ( !empty($cacheObj->error) ): ?>
      <div class="error">
        <?= $cacheObj->error ?>
      </div>
<?php elseif ( !empty($response->error) ): ?>
      <div class="error">
        <?= $response['error'] ?>
      </div>
<?php elseif ( is_null($callNumberTypes) ): ?>
      <div class="error">
        <?= 'PHP Error: Call number types json_decode() failure - ' . json_last_error_msg(); ?>
      </div>
<?php else: ?>
      <h1>FOLIO Call Number Types</h1>
      <div id="info">
        <p>The table below lists the call number types set in FOLIO and their UUIDs. The rightmost column lists the call number type SpineOMatic will use to parse the item call number stored in FOLIO when the barcode is scanned/entered. Refresh this page to view the most recent changes.</p>
        <p>To configure these values, make the following changes to the included config.php file:</p>
        <ol>
          <li>Set the default in the configuration file's <strong>Default SpineOMatic Call Number Type</strong> section. The default will be used by SpineOMatic if an item's FOLIO call number type UUID has not been assigned a SpineOMatic call number type.</li>
          <li>Copy/paste the FOLIO call number type UUIDs into the corresponding SpineOMatic call number type under the configuration file's <strong>FOLIO Call Number Type UUIDs</strong> section.</li>
        </ol>
        <p>The current default SpineOMatic call number type is: <strong><?= $som_type_names[$default_som_call_number_type] ?></strong></p>
      </div>
      <br>
      <table>
        <thead>
          <tr>
            <th>FOLIO Call Number Type</th>
            <th>UUID</th>
            <th>Assigned SpineOMatic Type</th>
          </tr>
        </thead>
        <tbody>
  <?php foreach ($callNumberTypes as $callNumberTypeObj): ?>
          <tr>
            <td><?= $callNumberTypeObj->name ?></td>
            <td><?= $callNumberTypeObj->id ?></td>
            <td><?= $som_type_names[$assigned_som_types[$callNumberTypeObj->id]] ?></td>
          </tr>
  <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3">
              Total: <?= count($callNumberTypes) ?>
            </td>
          </tr>
        </tfoot>
      </table>
<?php endif; ?>
    </div>
  </body>
</html>