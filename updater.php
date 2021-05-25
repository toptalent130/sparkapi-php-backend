<?php
define('debug', 0);
	
	//check if processing script is running.
	$options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "spider", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 3600,      // timeout on connect
        CURLOPT_TIMEOUT        => 3600,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
    );

    $ch      = curl_init('https://homeinmexico.com/wp-cron.php?import_key=Cs7jd5nANUgC&import_id=1&action=processing');
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );
	
    if(strpos($response, 'Request skipped'))
		die("Request skipped: Cron running");

	// send email
//$headers = 'Content-type: text/html; charset=iso-8859-1' . '\r\n';
//mail("Tarun <workbyday@gmail.com>","Processing Cron ", $content, $headers);	
		
require_once("lib/Core.php");
require_once("_initialization.php");
require_once("_functions.php");

define('MaxListings', false, true);   //Change 10 to false after testing


// $file_name = $folder_path . "final.simple.xml";
$file_name = "E:/php/apiclient1/apiclient/final.simple.xml";

$xml_handle = fopen($file_name,'w') or die("can't create / open file");

/*-------------------------- from main file -------------------------------------------*/
$existing_item_found	= false;
$propID					= array();
$listings_id			= array();
$my_listings_id			= array();
$new_added				= array();
$rtime_start			= microtime(true);

$api = new SparkAPI_APIAuth("pvr_elements_key_2", "DGoBsf14TNypWPWEGj8E_");
//$api->SetDeveloperMode(true);
$api->SetApplicationName("Elements-Realty-Group/1.1");

$result = $api->Authenticate();


if ($result === false) {
	echo "API Error Code: {$api->last_error_code}<br>\n";
	echo "API Error Message: {$api->last_error_mess}<br>\n";
	exit;
}

/*-------------------------- from main file -------------------------------------------*/

/*------------------- own listings -----------------------------------*/
zm_get_my_litings($api, $Parameters);
/*------------------- END own listings -----------------------------------*/

//$Parameters['_orderby']	= '-OriginalOnMarketTimestamp';	
/*------------------- Others listings -----------------------------------*/
//$bfore_start	= count($new_added);
//zm_get_listings();
//$after_start	= count($new_added);
//$added			= $after_start - $bfore_start;
// the message
//$msg = "$added listings added in last upadet.\n";


//foreach($new_added as $prop){
//	$ampi	= $arr_listing_id[$prop][1];
//	$msg   .= "<br><a href='https://homeinmexico.com/ampi$ampi'>$ampi</a>";
//}
// send email
$headers = 'Content-type: text/html; charset=iso-8859-1' . '\r\n';
//if(!debug and $added)
//	mail("John <john@homeinmexico.com>","Found new listings in last check - TEMP SITE", $msg, $headers);

/*------------------- END Others listings -----------------------------------*/

if( debug )
	print_r("\n\n\n\n\n\n\n");

$filtertime = date("Y-m-d\TH:i:s\Z", time()-3600);
$Parameters['_orderby']	= '-ModificationTimestamp';
$Parameters['_filter']	= "ModificationTimestamp gt $filtertime";

/*------------------- Others listings -----------------------------------*/
$last_mod = file_get_contents($folder_path . 'last_mod.check');
//if($last_mod + 6*60*60 < time() or isset($_GET['update'])){
	$bfore_start	= count($new_added);
	zm_get_listings('update', $existing_item_found);
	file_put_contents($folder_path . 'last_mod.check', time());
	$after_start	= count($new_added);
	$added			= $after_start - $bfore_start;
	// the message
	$msg = "$added listings updated in last update.\n".print_r($new_added, true);
	// send email
	//if(!debug and $added)
	//mail("John <john@homeinmexico.com>", "An update occurred TEMP NEW SITE!",$msg);
//}

/*------------------- END Others listings -----------------------------------*/

$api->SetCache( new SparkAPI_MySQLiCache($hostname = 'localhost', $database = 'homein27_wpimh', $username = 'homein27_wpimh', $password = '604(WpSS]2', $table_name = 'api_cache') );

fwrite($xml_handle, "<?xml version='1.0' encoding='UTF-8'?>\n<Root>\n");
$i=0;
if( debug )
	print_r($new_added);

//*
foreach($new_added as $id){
	$i++;
	//if(!$id) continue;
	$time_start	= microtime(true);
	
		echo $id;
		echo "<br />";
		$result = $api->GetListing(
			$id,
			array(
				"_expand"	=> "CustomFields,Photos,Rooms,Supplement,Units,Videos,VirtualTours,Documents",
			)
		);
		
		if($result && $result["Id"]){
			do_optimization($result);	
			add_elemnt_to_dom($inner_arr);
			$time_end	=  microtime(true);
			//echo $i . " face time".($time_end - $time_start) ."s  done in ".($time_end - $time_start) ."s with APINUM:" . $id . " <br>\n";
			//if(MaxListings and $i>MaxListings)
			//	echo 'MaxListings='.MaxListings."\n";
			//break;
		}
		if( debug )
			print_r($result);
			
			
}  //*/
fwrite($xml_handle, "</Root>");
//echo count($new_added)
//print_r($arr_listing_id);die;

$rtime_end	=  microtime(true);

if( debug )
	echo "\ncompleted in " .($rtime_end-$rtime_start) ."s\n";

//print_r($GLOBALS);   DELETE ALL // after this line
if(!debug and count($new_added)){
	

	get_headers('https://homeinmexico.com/wp-cron.php?import_key=Cs7jd5nANUgC&import_id=1&action=trigger', 1);
	
	//get_headers('https://homeinmexico.com/wp-cron.php?import_key=Cs7jd5nANUgC&import_id=1&action=processing', 1);
	
	
	
	//echo $content;
	
	update_json_to_db();        //add //to front of this line to get old result
	
	$file_name			= $folder_path . "listing-ids.json";
	$listing_ids		= fopen($file_name,'w');

	$listing_ids_json	= json_encode($arr_listing_id);
	fwrite($listing_ids, $listing_ids_json );
	
	
}


function update_json_to_db(){
	$servername = "localhost";
	$username = "homein27_wpimh";
	$password = "604(WpSS]2";

	//$conn = mysql_connect($servername, $username, $password);

	//$selected = mysql_select_db("homein27_wpimh",$conn);
	
	
	$conn = mysqli_connect($servername, $username, $password, "homein27_wpimh");


	mysqli_query($conn,"update wp_hlbu_options SET option_value = '$listing_ids_json' where option_name = 'listing_ids_json_data'" );
}
echo "DONE";