<?php

### Configuration ###

/*
 * Enter values between single quotes following equals signs or without quotes
 * where indicated. If setting values inside square brackets, separate
 * values with commas and optional spaces/new lines. Comment or uncomment an
 * optional parameter by entering a # symbol at the beginning of the line.
 */


/*
 * Section: FOLIO Okapi Domain
 *
 * Enter the FOLIO Okapi domain name (without https:// or trailing /).
 */

$folio_okapi_domain = '';  // Required


/*
 * Section: FOLIO API Credentials
 *
 * Enter the values for Okapi tenant, username and password. As an alternative
 * to username and password, enter a token assigned to the user.
 *
 * Locate the tenant and token values by logging into FOLIO with a user account
 * granted GET permission to the inventory/items API and access to the FOLIO
 * Developer section. Navigate to Apps > Settings > Developer > Set token;
 * the value for the token is in the "Authentication token (JWT)" field, and the
 * tenant value is listed under "tenant".
 *
 * Alternatively, locate the tenant and token values by logging into FOLIO with
 * the aforementioned user account and opening the web browser's web developer
 * tools. Click the Network tab, and click a FOLIO app or reload the page if an
 * app is already selected. Select a GET request to the domain beginning with
 * "okapi". The values for tenant and token are listed in the request headers as
 * "X-Okapi-Tenant" and "X-Okapi-Token", respectively.
 *
 * If the token parameter is uncommented or left blank, the script will use the
 * username and password to attempt creating a new token. This token will be
 * stored in the cache.json file and will expire after the number of days set.
 * Permissions for this file should allow write access to the web server user
 * (typically apache, www-data, etc.). The recommended permissions are octal
 * 600 ( rw- --- --- ). If setting these file permissions is not preferred or
 * possible, it is recommended to enter the token as described above. Otherwise,
 * the script will call the API to create a new token every time a barcode is
 * scanned/entered.
 */

$folio_okapi_tenant = '';  // Required
#$folio_okapi_token = '';  // Optional if username and password are set

$folio_username = '';  // Optional if token is set
$folio_password = '';  // Optional if token is set

$token_expiration_days = 7;  // Required; do not use quotes around the number.


/*
 * Section: Barcode Format Validation
 *
 * To validate the format of barcodes scanned/entered into SpineOMatic,
 * uncomment the parameter and set the value of $barcode_regexp to a regular
 * expression pattern that will return true for invalid input.
 *
 * Examples:
 *  Barcode should only be numbers.
 *   $invalid_barcode_regexp = '/\D/';
 *
 *  Barcode should only be case-insensitive letters and/or numbers.
 *   $invalid_barcode_regexp = '/[^A-Za-z0-9]/';
 */

#$invalid_barcode_regexp = '/\D/';  // Optional


/*
 * Section: Default SpineOMatic Call Number Type
 *
 * Set the SpinOMatic call number type to use if an item's FOLIO call number
 * type UUID does not match any of those set in the next section. If only one
 * call number type will be used, set it here and do not set any UUIDs in the
 * next section.
 *
 * SpineOMatic Call Number Types:
 *  0 - Library of Congress and LC Children's Literature
 *  1 - Dewey Decimal
 *  2 - National Library of Medicine (NLM)
 *  3 - Superintendent of Documents (SuDoc)
 *  4 - SpineOMatic User-Defined Scheme
 */

$default_som_call_number_type = 0;  // Required; do not use quotes around the number.


/*
 * Section: FOLIO Call Number Type UUIDs
 *
 * Browse to the included call_number_types.php and enter UUIDs of FOLIO call
 * number types for each type supported by SpineOMatic. If only one call number
 * type will be used, do net set any UUIDs and set the default call number type
 * to use in the previous section.
 *
 * Example:
 *  // Library of Congress and LC Children's Literature
 *  $lc_uuids = [
 *      '1234abcd-12c4-a1b2-97f1-123456abcdef',
 *      '2a31a7cd-e261-b5d7-84fb-654321fedcba',
 *  ];
 *
 *  This will cause FOLIO items with either of the entered call number type
 *  UUIDs to be parsed as Library of Congress and LC Children's Literature call
 *  numbers in SpineOMatic.
 */

// These parameters are optional, but do not comment them. Instead leave them
// empty (example: $lc_uuids = [ ];).

// Library of Congress and LC Children's Literature
$lc_uuids = [
    '',
];

// Dewey Decimal
$dewey_uuids = [
    '',
];

// National Library of Medicine (NLM)
$nlm_uuids = [
    '',
];

// Superintendent of Documents (SuDoc)
$sudoc_uuids = [
    '',
];

// SpineOMatic User-Defined Scheme
$som_uuids = [
    '',
];


/*
 * Section: Formatted (Extended) Call Numbers
 *
 * To add additional FOLIO fields to call numbers parsed by SpineOMatic, edit
 * the formats below.
 *
 * NOTE: SpineOMatic handles the call number prefix separately. Include it
 *  by editing SpineOMatic's configuration.
 *
 * Syntax:
 *  {{field}} will include the field, as is, if set in FOLIO.
 *  { {field}} will include the field, if set in FOLIO, with a prepended space.
 *  { v.{field}} will include the field, if set, with a prepended " v."
 *  { c.{field>#}} will include the field with a prepended " c."; and it will
 *    only include the field if set in FOLIO and the first number found in the
 *    field is greater than #. Greater than ">", less than "<", and equals "="
 *    are valid operators. ">" and "<" may be used together to create ranges
 *    (e.g. >3<20) to only include fields wherein the first number of the field
 *    is greater than 3 and less than 20.
 *
 * Currently Supported FOLIO Fields:
 *  item_call_number
 *  item_call_number_suffix
 *  item_copy_number
 *  item_volume
 *
 * Example:
 *  {{item_call_number}}{ {item_call_number_suffix}}{ v.{item_volume}}{ c.{item_copy_number>1<10}}
 *
 *  The call number passed to SpineOMatic will consist of the call number set in
 *  FOLIO; the call number suffix preceded by a space, if a suffix is set in
 *  FOLIO; the item's volume preceded by a space and "v.", if volume is set in
 *  FOLIO; and the item's copy number preceded by a space and "c." if copy
 *  number is set in FOLIO and the first number in the field is greater than 1
 *  and less than 10.
 *
 *  TIP: These call number formats may be used with SpineOMatic's feature to
 *   add breaks before/after characters ("Call Number Format" tab > call number
 *   type tab > "Other:" radio buttons).
 */

// This parameter is required; rather than empty values, enter the default value
// of '{{item_call_number}}'.

$formatted_call_numbers = [
    // Library of Congress and LC Children's Literature
    '{{item_call_number}}',

    // Dewey Decimal
    '{{item_call_number}}',

    // National Library of Medicine (NLM)
    '{{item_call_number}}',

    // Superintendent of Documents (SuDoc)
    '{{item_call_number}}',

    // SpineOMatic User-Defined Scheme
    '{{item_call_number}}',
];



/*
 * Do not edit anything below.
 */

$folio_call_number_type_uuids = array(
    $lc_uuids,
    $dewey_uuids,
    $nlm_uuids,
    $sudoc_uuids,
    $som_uuids
);
