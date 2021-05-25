<?php

	//ini_set('max_execution_time', 3000);
    //ini_set('memory_limit', '2048M'); 	// max memory size 2GB
	header('Content-Type: text/html; charset=UTF-8');
	
	define('limit_img', false);
	
$max_matched	= 50;
	
	$flag_test = true;	//	test local = false		// test online = true
	$flag_seba = false;
	if($flag_test == true)		//	on line version
	{
		if($flag_seba == true) {
			$folder_path = __DIR__ ."\\";
			
		}
		else{
			$folder_path = $_SERVER['DOCUMENT_ROOT'] . "/apiclient/";
		}
	}
	else{
		$folder_path = __DIR__ ."\\";
	}

	
$Parameters = array(
	"_select"		=> "ListingId,OriginalOnMarketTimestamp",
	'_pagination'	=> 1,
	'_limit'		=> 25,
	'_page'			=> 1,
	//'_orderby'		=> '-OriginalOnMarketTimestamp',
);
		
	$file_name = $folder_path . "debug.txt";
	// $file_name = "E:/php/apiclient1/apiclient/debug.txt";
    $debug = fopen($file_name,'w') or die("can't create / open file");	
	
	$TotalPages = 1;
	$array_ID = array();
	

	
	$arr_listing_id		= array();
	$file_name			= $folder_path . "listing-ids.json";
	if (!file_exists('backup')) {
		mkdir('backup', 0755, true);
	}
	$directory = 'backup/';
	if(file_exists($file_name))
		copy($file_name, 'backup/backup_json_id_' . time());
	
	if(file_exists($file_name)){
		$listing_ids		= fopen($file_name,'r');
		$arr_listing_id		= json_decode(fread($listing_ids, filesize($file_name)));
		fclose($listing_ids);
	}
	if(!is_array($arr_listing_id)) $arr_listing_id = (array)$arr_listing_id;
	
	
	$str_NA = "N/A";
	$feet_to_sq_meter_constant = 10.76;
	$value_14 = "";	//	important , keeps url photo


		
	$State_arr = array(	"JA"=>"Jalisco","NA"=>"Nayarit");
		
	$PropertyType = array("A"=>"Condo","B"=>"House","E"=>"Land","F"=>"Commercial","G"=>"Business","H"=>"Fractional","I"=>"Multi-family");

	$keep_in_CSV = array(
		"ListingId"				=>1,
		"ListPrice"				=>2,
		"MajorChangeType"		=>3,
		"PropertyType"			=>4,
		"BathsFull"				=>5,
		"BathsHalf"				=>6,
		"BathsTotal"			=>7,
		"BedsTotal"				=>8,
		"BuildingAreaTotal"		=>9,
		"Total M2 const"		=>10,				// to check if has value , if not take value from BuildingAreaTotal / 10.76 , if no one has value add N/A
		"Lot Square Feet"		=>11,				// to add math , = Lot M2 * 10.76  feet_to_sq_meter_constant
		"Lot M2"				=>12,
		"Primary View"			=>13,
		"Furniture"				=>15,
		"YearBuilt"				=>16,
		"Parking"				=>17,
		"ArchitecturalStyle"	=>18,
		"$/m2 construction"		=>19,			// to add math		= ListPrice / Total M2 const
		"$/ft2 construction"	=>20,			// to add math		= ListPrice / BuildingAreaTotal
		"$/m2 land"				=>21,					// to add math		= ListPrice / Lot M2
		"$/ft2 land"			=>22,					// to add math		= ListPrice / Lot Square Feet
		"City"					=>23,
		"Latitude"				=>24,
		"Longitude"				=>25,
		"MLSAreaMinor"			=>26,
		"StateOrProvince"		=>27,
		"StreetAdditionalInfo"	=>28,
		"StreetDirSuffix"		=>29,
		"StreetName"			=>30,
		"StreetNumber"			=>31,
		"StreetSuffix"			=>32,
		"UnparsedAddress"		=>33,
		"Address"				=>34,
		"Cooling"				=>35,
		"Flooring"				=>36,
		"Inclusions"			=>37,
		"RoomsDescription"		=>39,
		"RoomsList"				=>40,
		"RoomsTotal"			=>41,
		"Stories"				=>42,
		"TaxAmount"				=>43,
		"Unit Details"			=>44,
		"Road Type"				=>45,
		"Common Amenities"		=>46,
		"Appliances"			=>47,
		"Devices"				=>48,
		"Construction"			=>49,
		"Location"				=>50,
		"HOA Info"				=>51,
		"Potential Use"			=>52,
		"Title"					=>53,
		"Secondary View"		=>54,
		"Mstr Plan Community"	=>55,
		"Contract Data"			=>56,
		"Const"					=>57,
		"ListingKey"			=>58,
		"MlsId"					=>59,
		"PhotosCount"			=>60,
		"VirtualToursCount"		=>61,
		"VideosCount"			=>62,
		"ModificationTimestamp"	=>63,
		"OnMarketDate"			=>64,
		"PriceChangeTimestamp"	=>65,
		"PublicRemarks"			=>66,
		"Sewer"					=>67,
		"Sewage"				=>71,
		"WaterSource"			=>68,
		"Water"					=>70,
		"Electricity"			=>69,
		"Electric/Electrico"	=>72,
		"Connectivity"			=>73,

		"City Final"			=>74,
		"Area SEO Field"		=>75,
		"Pools"					=>76,
		"Beds Total"			=>77,
		"List Price"			=>78,

		"VirtualTours"			=>79,
		"Videos"				=>81,

		"Photos"				=>84,
		"Amenities"				=>85,
		"MLSAreaMajor"			=>86,
		"MLSAreaMinor"			=>87,
		"MlsStatus"				=>88,
		"PropertyClass"			=>89,
		"Supplement"			=>90,
		"decreasesincelist"		=>91,
		"CurrentPrice"			=>92,
		"Photos"				=>93,
		"SubdivisionName"		=>93,
		"Ocean Front Meters"	=>94,
		"Floor Number"			=>95,
		"# Units in Develop"	=>96,
		"Flooring Type"			=>97,
		"Decks/Patios SqFt"		=>98,
		"Decks/Patios M2"		=>99,
		"Furnished"				=>100,
		//"Featured"			=>101, // resurved for feartured own listings
		"OriginalOnMarketTimestamp"	=>102, // will be convert to days on market
		"General Description" =>105
		);

	$CustomFields = array(	
		"Address"				=>34,
		"Unit Details"			=>44,
		"Road Type"				=>45,
		"Common Amenities"		=>46,
		"Appliances"			=>47,
		"Devices"				=>48,
		"Electricity"			=>69,
		"Construction"			=>49,
		"Water"					=>70,
		"Sewage"				=>71,
		"Location"				=>50,
		"HOA Info"				=>51,
		"Potential Use"			=>52,
		"ElectricElectrico"		=>72,
		"Title"					=>53,
		"Lot M2"				=>12,
		"Total M2 const"		=>10,
		"Primary View"			=>13,
		"Secondary View"		=>54,
		"Mstr Plan Community"	=>55,
		"Contract Data"			=>56,
		"Connectivity"			=>73,
		"Furniture"				=>15,
		"Parking"				=>17,
		"Const"					=>57,
		"Pet Friendly"			=>14,
		"VirtualTours"			=>79,						
		"Videos"				=>81,
		"Photos"				=>84,
		"Amenities"				=>85,
		"Ocean Front Meters"	=>94,
		"Floor Number"			=>95,
		"# Units in Develop"	=>96,
		"Flooring Type"			=>97,
		"Decks/Patios M2"		=>99,
		"Furnished"				=>100,
		"General Description"	=>105
	);

	$Headings = array(
		"AMPINum"				=>1,
		"ListPrice"				=>2,
		"MajorChangeType"		=>3,
		"PropertyType"			=>4,
		"BathsFull"				=>5,
		"BathsHalf"				=>6,
		"BathsTotal"			=>7,
		"BedsTotal"				=>8,
		"ConstFT2"				=>9,
		"ConstM2"				=>10,
		"LotFT2"				=>11,
		"LotM2"					=>12,
		"PrimaryView"			=>13,
		"Furniture"				=>15,
		"YearBuilt"				=>16,
		"Parking"				=>17,
		"ArchitecturalStyle"	=>18,
		"ConstUSD_M2"			=>19,
		"ConstUSD_FT2"			=>20,
		"LotUSD_M2"				=>21,
		"LotUSD_FT2"			=>22,
		"City"					=>23,
		"Latitude"				=>24,
		"Longitude"				=>25,
		"State"					=>27,
		"PropName"				=>28,
		"PropNameSuffix"		=>29,
		"AdrStrName"			=>30,
		"AdrStrNum"				=>31,
		"AdrStrSuffix"			=>32,
		"AdrWHOLE"				=>33,
		"Stories"				=>42,
		"UnitDetails"			=>44,
		"RoadType"				=>45,
		"CommonAmenities"		=>46,
		"Appliances"			=>47,
		"Devices"				=>48,
		"location"				=>50,
		"HOAInfo"				=>51,
		"PotentialUse"			=>52,
		"Title"					=>53,
		"SecondaryView"			=>54,
		"MstrPlanCommunity"		=>55,
		"Financing"				=>56,
		"ListingKey"			=>58,
		"MlsId"					=>59,
		"PhotosCount"			=>60,
		"VirtualToursCount"		=>61,
		"VideosCount"			=>62,
		"OnMarketDate"			=>64,
		"PropDescription"		=>66,
		"Electricity"			=>69,// also 72
		"Water"					=>70,// also 68
		"Sewage"				=>71,// also 67
		"UtilConnect"			=>73,	
		"CityFinal"				=>74,
		"AreaSEOField"			=>75,
			
		"VirtualToursName"		=>79,		
		"VirtualToursURL"		=>80,				
		"VideosName"			=>81,			
		"VideosCaption"			=>82,			
		"VideosURL"				=>83,
		
		"Photos"				=>84,
		
		"Amenities"				=>85,
		"MLSAreaMajor"			=>86,
		"MLSAreaMinor"			=>87,
		"MlsStatus"				=>88,
		"PropertyClass"			=>89,
		"Supplement"			=>90,
		"decreasesincelist"		=>91,
		"CurrentPrice"			=>92,
		"SubdivisionName"		=>93,
		"OceanFrontMeters"		=>94,
		"FloorNumber"			=>95,
		"UnitsInDevelop"		=>96,
		"FlooringType"			=>97,
		"DecksPatiosSqFt"		=>98,
		"DecksPatiosM2"			=>99,
		"Furnished"				=>100,
		"Featured"				=>101,
		"OriginalOnMarketTimestamp"			=>102,
		"PostSlug"				=>103,
		"FloorPlan"				=>104,
		"GeneralDescription"	=>105,
		
		);

	$max = 105;
	
	for($i=1 ; $i <= $max; $i++) {
		$inner_arr[$i] = "";	
	}