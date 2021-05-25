<?php
define( 'debug', 0 );
//ini_set( 'max_execution_time', 3600 );
//ini_set( 'memory_limit', '1024M' );  // max memory size 1GB
header( 'Content-Type: text/html; charset=UTF-8' );

$servername = "localhost";
$username = "homein27_wpimh";
$password = "604(WpSS]2";
//$conn        = mysql_connect( $servername, $username, $password );

//$selected = mysql_select_db("homein27_wpimh",$conn);

$conn = mysqli_connect($servername, $username, $password, "homein27_wpimh");

$max_matched = 50;
$flag_test   = true; // test local = false  // test online = true
$flag_seba   = false;

if ( $flag_test == true ) {  // on line version
	if ( $flag_seba == true ) {
		$folder_path = __DIR__ ."\\";
	} else {
		$folder_path = $_SERVER['DOCUMENT_ROOT'] . "/apiclient/";
	}
} else {
	$folder_path = __DIR__ ."\\";
}

require_once "lib/Core.php";
require_once("_initialization.php");
define( 'MaxListings', false, true );

$api = new SparkAPI_APIAuth( "pvr_elements_key_2", "DGoBsf14TNypWPWEGj8E_" );
//$api->SetDeveloperMode(true);
$api->SetApplicationName( "Elements-Realty-Group/1.1" );

$result = $api->Authenticate();
if ( $result === false ) {
	echo "API Error Code: {$api->last_error_code}<br>\n";
	echo "API Error Message: {$api->last_error_mess}<br>\n";
	exit;
}

global $listings_id;

/* Fetching the existing posts */
$result = mysqli_query($conn, "SELECT p.ID, pm.meta_value AS ampi
												FROM wp_hlbu_posts AS p
													INNER JOIN wp_hlbu_postmeta AS pm ON p.ID = pm.post_id
												WHERE pm.meta_key = 'mls_ampi_num'" );
$__listings;
while ( $row=mysqli_fetch_assoc( $result ) ) {
	$ampi = $row['ampi'];
	$pid  = $row['ID'];
	$__listings[$ampi] = $pid;
}

/* Fetching the current listings */
$Parameters = array(
	"_select"     => "ListingId,OriginalOnMarketTimestamp",
	'_pagination' => 1,
	'_limit'      => 25,
	'_page'       => 1,
);
$Parameters['_orderby'] = '-OriginalOnMarketTimestamp';
deleter_get_listings();

foreach ( $__listings as $ampi => $pid ) {
	if ( in_array( $ampi, $listings_id ) ) {
		echo "<br />Listing Activate :".$pid;
		mysqli_query($conn, "call listing_activate ( $pid );" );
	} else {
		echo "<br />Listing Expired :".$pid;
		mysqli_query($conn, "call listing_expire( $pid );" );
	}
}

function deleter_get_listings() {
	global $api, $Parameters, $result, $existing_item_found;
	$Parameters['_page'] = 1;
	$result = $api->GetListings( $Parameters );
	//print_r($result);
	deleter_set_listings_id( $result );
	for ( $i=2;$i<=$api->total_pages;$i++ ) {
		$Parameters['_page'] = $i;
		$result     = $api->GetListings( $Parameters, '200m' );
		deleter_set_listings_id( $result );
	}
}

function deleter_set_listings_id( $result, $my = false ) {
	global $listings_id;
	if ( $result ) {
		foreach ( $result as $key => $value ) {
			$listings_id[] = $value['StandardFields']['ListingId'];
		}
	}
}
