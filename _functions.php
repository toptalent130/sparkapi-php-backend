<?php

function populate_listings_id ($result, $my = false) {
	global $listings_id;
	if($my)
		global $my_listings_id;
	
	
	if ($result) {
		foreach($result as $key => $value) {
			$listings_id[$value['Id']] = $value['Id'];
			if($my)
				$my_listings_id[] = $value['Id'];
		}				
	}	
}

function do_optimization($input){
	global $keep_in_CSV;
	global $CustomFields;
	
	global $CSV_URL_handle;
	global $ListingId;
	global $Old_ListingId;
	global $index_url;
	global $inner_arr;
	global $flag_name;
	global $max;
	global $PropertyType, $State_arr, $feet_to_sq_meter_constant, $str_NA  ;
	
	for($i=1 ; $i <= $max; $i++) {
		$inner_arr[$i] = "";	
	}
	
	parse_JSON_string ($input);
	
	if(in_array("Pet Friendly", $inner_arr[105])){ //adding as common amenities
		if(is_array($inner_arr[46])){
			array_push($inner_arr[46],"Pet Friendly");
		}
		else{
			$inner_arr[46]=array("Pet Friendly");
		}
	}
	
	global $LOG_handle;
	//fwrite($LOG_handle, print_r( $inner_arr[46],true));
	$temp_number = str_replace("\"", "", $inner_arr[58]);
	$inner_arr[58] = $temp_number ;
	
	$temp_number = str_replace("\"", "", $inner_arr[59]);
	$inner_arr[59] = $temp_number ;				
	
	$decreasesincelist = 100- (100*($inner_arr[92]/$inner_arr[2]));
	$inner_arr[91] = number_format((float)$decreasesincelist, 1, ".", "" ) . "%";
	
	//"ArchitecturalStyle"=>18,
	if($inner_arr[18] == "1LevApt") {

		$inner_arr[18] = "1 Level";
	}
	
	$inner_arr[14] = str_replace(1 , "Yes" , $inner_arr[14]);
	//$inner_arr[14] = str_replace(0 , "No" , $inner_arr[14]);
	
	$ListPrice = $inner_arr[2];
	$PropertyClass = $inner_arr[3];
	$beds = $inner_arr[8];
	
	//$inner_arr[77] = find_beds_string ($beds);
	//$inner_arr[78] = find_price_string ($ListPrice);					

	$str_to_replace = $inner_arr[4];
	$inner_arr[4] =  $PropertyType[$str_to_replace]   ;
	
	//"StateOrProvince"=>27,
	$state = $inner_arr[27];
	$inner_arr[27] = $State_arr[$state];
	
	if ($inner_arr[23] != "Puerto Vallarta") {
		$inner_arr[74] = $inner_arr[87]; //74 =CityFinal
		$inner_arr[75] = $inner_arr[87];//26= MLSAreaMinor
	}
	else{
		$inner_arr[74] = $inner_arr[23]; //23=City
		$inner_arr[75] = $inner_arr[87];
	}

	if(strlen($inner_arr[9]) <= 1 and strlen($inner_arr[10]) <= 1) {

		$inner_arr[9] =  $str_NA ;
		$inner_arr[10] =  $str_NA ;
		
		$inner_arr[19] =  $str_NA ;
		$inner_arr[20] =  $str_NA ;
		
	}
	elseif (strlen($inner_arr[9]) > 1 and strlen($inner_arr[10]) <= 1) {

		$inner_arr[10] = $inner_arr[9] / $feet_to_sq_meter_constant ;  
		
		$inner_arr[19] =  number_format($ListPrice / $inner_arr[10] , 2 , '.', '')  ;
		$inner_arr[20] =  number_format($ListPrice / $inner_arr[9] , 2 , '.', '')  ;
		
		$inner_arr[10] =  number_format($inner_arr[10] , 2 , '.', '')  ;
		$inner_arr[9] =  number_format($inner_arr[9] , 2 , '.', '')  ;
	}
	elseif(strlen($inner_arr[9]) <= 1 and strlen($inner_arr[10]) > 1) {

		$inner_arr[9] = $inner_arr[10] * $feet_to_sq_meter_constant ;	
		
		$inner_arr[19] =  number_format($ListPrice / $inner_arr[10] , 2 , '.', '')  ;
		$inner_arr[20] =  number_format($ListPrice / $inner_arr[9] , 2 , '.', '')  ;
		
		$inner_arr[9] =  number_format($inner_arr[9], 2 , '.', '')  ;
		$inner_arr[10] =  number_format($inner_arr[10], 2 , '.', '')  ;						
	}
	elseif(strlen($inner_arr[9]) > 1 and strlen($inner_arr[10]) > 1) {

		$inner_arr[19] =  number_format($ListPrice / $inner_arr[10] , 2 , '.', '')  ;
		$inner_arr[20] =  number_format($ListPrice / $inner_arr[9] , 2 , '.', '' )  ;
		
		$inner_arr[9] =  number_format($inner_arr[9] , 2 , '.', '')  ;	
		$inner_arr[10] =  number_format($inner_arr[10] , 2 , '.', '')  ;						
	}
	
	//	-------------------------------------------------------------------------------

	if(strlen($inner_arr[11]) <= 1 and strlen($inner_arr[12]) <= 1) {

		$inner_arr[11] =  $str_NA ;
		$inner_arr[12] =  $str_NA ;
		
		$inner_arr[21] =  $str_NA ;
		$inner_arr[22] =  $str_NA ;
	}
	elseif (strlen($inner_arr[11]) > 1 and strlen($inner_arr[12]) <= 1) {

		$inner_arr[12] = $inner_arr[11] / $feet_to_sq_meter_constant ;
		
		$inner_arr[21] =  number_format($ListPrice / $inner_arr[12] , 2 , '.', '')  ;
		$inner_arr[22] =  number_format($ListPrice / $inner_arr[11] , 2 , '.', '' )  ;
		
		$inner_arr[12] =  number_format($inner_arr[12] , 2 , '.', '' )  ;
		$inner_arr[11] =  number_format($inner_arr[11] , 2 , '.', '' )  ;
	}
	elseif(strlen($inner_arr[11]) <= 1 and strlen($inner_arr[12]) > 1) {

		$inner_arr[11] = $inner_arr[12] * $feet_to_sq_meter_constant ;
		
		$inner_arr[21] =  number_format($ListPrice / $inner_arr[12] , 2 , '.', '')  ;
		$inner_arr[22] =  number_format($ListPrice / $inner_arr[11] , 2 , '.', '' )  ;
		
		$inner_arr[11] =  number_format($inner_arr[11] , 2 , '.', '' )  ;
		$inner_arr[12] =  number_format($inner_arr[12] , 2 , '.', '' )  ;						
	}
	elseif(strlen($inner_arr[11]) > 1 and strlen($inner_arr[12]) > 1) {

		$inner_arr[21] =  number_format($ListPrice / $inner_arr[12] , 2 , '.', '')  ;
		$inner_arr[22] =  number_format($ListPrice / $inner_arr[11] , 2 , '.', '' )  ;
		
		$inner_arr[11] =  number_format($inner_arr[11] , 2 , '.', '' )  ;	
		$inner_arr[12] =  number_format($inner_arr[12] , 2 , '.', '' )  ;						
	}
	
	$inner_arr[2] = number_format($ListPrice , 0 , '.', '' ) ;
						
	$arr_65 = str_replace("\"", "", $inner_arr[65]);
	$inner_arr[65] = substr($arr_65 , 0 , 10) ;					
	
	$arr_64 = str_replace("\"", "", $inner_arr[64]);
	$inner_arr[64] =  substr($arr_64 , 0 , 10) ;						
	
	$arr_63 = str_replace("\"", "", $inner_arr[63]);
	$inner_arr[63] =  substr($arr_63 , 0 , 10) ;
	
	$findme_0 = "www.";
	$findme_1 = ".com";
	
	$arr_66 = $inner_arr[66];
	$arr_66 = $arr_66 . " ";
	$pos_0 = stripos($arr_66, $findme_0);
	$pos_1 = stripos($arr_66, $findme_1);
	
	$to_strip_XML = array("");
	$arr_66 = str_replace($to_strip_XML , "" ,$arr_66);
	
	if ($pos_0 !== false and $pos_1 !== false) {
		$str_01 = substr($arr_66 , 0 , $pos_0);
		//echo $str_01 . "<br>";
		$str_02 = substr($arr_66 , $pos_1 + 4 , strlen($arr_66) - $pos_1 - 4 );
		//echo $str_02 . "<br>";
		$inner_arr[66] = iconv("UTF-8", "UTF-8", $str_01 . $str_02) ;
	}
	else{
		$arr_66 = iconv("UTF-8", "UTF-8", $arr_66);		//	UTF-8
		$inner_arr[66] = $arr_66  ;
	}	
	
	$inner_arr[33] = ucwords(strtolower($inner_arr[33]));	//
	$inner_arr[33] = str_replace(array("S/N ", "S/N"), "", $inner_arr[33]);	//
	$inner_arr[31] = str_replace(array("S/N ", "S/N"), "", $inner_arr[31]);	//
	
	$inner_arr[34] = str_replace("Community:", "", $inner_arr[34]);	//	replace Community: with nothing
	
	$arr_28 =  $inner_arr[28];
	$arr_29 = $inner_arr[29];
	$roman_arr	=	array(
						'I'		=> 1,
						'II'	=> 2,
						'II'	=> 3,
						'IV'	=> 4,
						'V'		=> 5,
						'VI'	=> 6,
						'VII'	=> 7,
					);
	
	$arr_28 = str_replace(array("II", 'III',","), array(2,3,''), $arr_28);
	$arr_28 = str_replace('  ', ' ', $arr_28);
	$arr_28 = ucwords(strtolower($arr_28));	//	
	$inner_arr[28]	= $arr_28;
	
	$arr_29 = str_replace(',', '', $arr_29);
	$arr_29 = str_replace('  ', ' ', $arr_29);
	$inner_arr[29]	= $arr_29;
	
	//fwrite($LOG_handle1 , $arr_28 . "-----arr_28_last\r\n");
	//fwrite($LOG_handle1 , $arr_29 . "-----arr_29_first\r\n");
	//fwrite($LOG_handle1 , strtolower (substr($arr_28 , strlen($arr_28) - strlen($arr_29) ,strlen($arr_29))) . "----- stripos arr_28_last\r\n");
	
	/*if(strlen($arr_29) > 0 and $tmp_28 = str_replace($arr_29, "", $arr_28) and $tmp_28 != $arr_28) {
		$inner_arr[28] = ucwords(trim($tmp_28));
	}*/
	$arr_28 = str_replace(strtolower($arr_29), "", strtolower($arr_28));
	$inner_arr[28] = ucwords(trim($arr_28));
	
	$tmp_28		= str_replace('#','',$arr_28);
	$tmp_28_1	= str_replace(strtolower($arr_29), "", strtolower($tmp_28));
	if($tmp_28 != $tmp_28_1)
		$inner_arr[28] = ucwords(trim($tmp_28_1));
	
	
	
	$inner_arr[30] = ucwords(strtolower($inner_arr[30]));
	
	if(strtolower($inner_arr[55]) == "select one") {

		$inner_arr[55] = "No";
	}
	$inner_arr[74]	= trim(str_replace(array('East','West','South','east','west','south'), '', $inner_arr[74] ));
	$arr_103	= $inner_arr[74] . '-' . $inner_arr[4] . '-' . $inner_arr[28] . '-' . $inner_arr[29] . '-AMPI' . $inner_arr[1];
	$arr_103	= str_replace( array('   ', '  ',' ', "''"), '-', strtolower(trim($arr_103)));
	
	$inner_arr[103]	= str_replace(array('----','---','--'),'-',$arr_103);
	
	//$inner_arr[74]	= ucwords($inner_arr[74]);
	$inner_arr[75]	= ucwords($inner_arr[75]);
	$inner_arr[902]	= $inner_arr[102];
	$inner_arr[102]	= strtotime($inner_arr[102]);
	
	//fwrite($LOG_handle, print_r( $inner_arr[46],true));
	$i=0;
	foreach($inner_arr[84] as $k=>$photo){
		$i++;
		$arr_84_name	= $photo['Name'];
		if(trim($arr_84_name) != '')
			$arr_84_name .= ' '. $inner_arr[28] .' '. $inner_arr[29];
		else
			$arr_84_name  = $inner_arr[28] .' '. $inner_arr[29] .' '. $inner_arr[74] .' '. $inner_arr[4] .  " For Sale-MLS#" . $inner_arr[1] ;
		$inner_arr[84][$k]['Name']	= $arr_84_name;
		
		$arr_84_caption	= $photo['Caption'];
		if(trim($arr_84_caption) == '') 
			$arr_84_caption	= $arr_84_name;
		$inner_arr[84][$k]['Caption']	= ucwords($arr_84_caption);
	}
	
	return $inner_arr;
}


function parse_JSON_string ($var_2) {
	global $keep_in_CSV;
	global $CustomFields;
	
	global $CSV_URL_handle;
	global $ListingId;
	global $Old_ListingId;
	global $index_url;
	global $inner_arr;
	global $flag_name;
	
	$Type_12 ="";
	$Name_12 ="";
	$Caption ="";
	$ObjectHtml ="";
	
	global $value_14 ;
	
	$to_replace = array("********");
	
	//var_dump($var_2);

	if (gettype($var_2)== "array" or gettype($var_2)== "object") {
		foreach($var_2 as $key_1 => $value_1) {
			
			if (gettype($value_1)== "string" or gettype($value_1) == "boolean" or gettype($value_1) == "float" or gettype($value_1) == "integer" or gettype($value_1) == "double" or is_null($value_1)) {

				if (array_key_exists($key_1 , $keep_in_CSV)) {
					$temp_index = $keep_in_CSV[$key_1];
					
					if ($key_1 == "ListingId") {
						$ListingId = $value_1;
						$inner_arr[$keep_in_CSV[$key_1]] =  $value_1 ;
					}
					else{
						$inner_arr[$keep_in_CSV[$key_1]] = str_replace($to_replace , "" , $value_1);
					}
				}	
			}
			else{	// if object or array not "string" or "boolean" or "float" "integer" "double" or is_null
				
				if (array_key_exists($key_1 , $keep_in_CSV)) {
					//$inner_arr[$keep_in_CSV[$key_1]] = $key_1;
					
					if($key_1 == "Electricity" or $key_1 == "Electric/Electrico"){
						//echo "\n$key_1:";
						//print_r($value_1);
						if(isset($value_1) and is_array($value_1)){
							$elec	= array();
							foreach($value_1 as $val_elec)
								$elec[]	= key($val_elec);
							$inner_arr[69] = implode(', ', $elec);
							
						}
						//print_r($inner_arr[69]);
						//echo "\n\n\n";
					}
					elseif($key_1 == "Water" or $key_1 == "WaterSource"){
						//echo "\n$key_1:";
						//print_r($value_1);
						if(isset($value_1) and is_array($value_1)){
							foreach($value_1 as $key_water=>$val_water){
								$inner_arr[70] = $key_1 == "WaterSource"?$key_water:key($val_water);
							}
						}
						//print_r($inner_arr[70]);
						//echo "\n\n\n";
					}
					elseif($key_1 == "Sewage" or $key_1 == "Sewer"){
						//echo "\n$key_1:";
						//print_r($value_1);
						if(isset($value_1) and is_array($value_1)){
							foreach($value_1 as $key_sewage=>$val_water){
								$inner_arr[71] = $key_1 == "Sewer"?$key_sewage:key($val_water);
							}
						}
						//print_r($inner_arr[71]);
						//echo "\n\n\n";
					}
					elseif (array_key_exists($key_1 , $CustomFields)) {

						//echo "................................................array_key_exists--CustomFields -" . $key_1 . "<br>";
						
						$str_10 = "";
						$flag_name = "Photos";
						
						if ($key_1 == "VirtualTours") {
							$flag_name = "VirtualTours";
							//echo "................................................flag_name" . $flag_name . "<br>";

							foreach($value_1 as $key_10 => $val_10) {
								foreach($val_10 as $key_12 => $val_12) {
									//echo $val_12 . "===========VirtualTours===key_12=======" . $key_12 . "<br>";

									if ($key_12 == "Type") {
										$Type_12 = $val_12;
									}
									elseif ($key_12 == "Name") {
										$Name_12 = $val_12;
									}

									elseif ($key_12 == "Uri") {
										$ObjectHtml = get_video_id($val_12) ? get_video_id($val_12) : $val_12;
									}
								}
								//echo $Type_12 . "---" . $Name_12 . "---" . $ObjectHtml . "<br>";
								//ECHO "<br>";
								
								if($Type_12 == "unbranded") {
									//		"VirtualTours Name"=>79,"Virtual Tours URL"=>80,
								
									$inner_arr[79] = $Name_12;
									$inner_arr[80] = $ObjectHtml;
								}
							}
						}
						elseif ($key_1 == "Videos"){
							$flag_name = "Videos";
							//echo "................................................flag_name" . $flag_name . "<br>";
							foreach($value_1 as $key_10 => $val_10) {
								foreach($val_10 as $key_12 => $val_12) {
									//echo $val_12 . "===========Videos===key_12=======" . $key_12 . "<br>";

									if ($key_12 == "Type") {
										$Type_12 = $val_12;
									}
									elseif ($key_12 == "Name") {
										$Name_12 = $val_12;
									}
									elseif ($key_12 == "Caption") {
										$Caption = $val_12;
									}
									elseif ($key_12 == "ObjectHtml") {
										$ObjectHtml = get_video_id($val_12) ? get_video_id($val_12) : $val_12;
									}
								}
								//echo $Type_12 . "---" . $Name_12 . "---" . $Caption . "---" . $ObjectHtml . "<br>";
								//ECHO "<br>";
								
								if($Type_12 == "unbranded") {
									//"Videos"=>81,"Videos 1"=>82,	"Videos 2"=>83,
									$inner_arr[83] = $ObjectHtml;
								}
							}
						}
						elseif ($key_1 == "Photos") {
							$flag_name = "Photos";
							$ix=0;
							global $skip_in_photo_name;
							foreach($value_1 as $index => $image){
								$ix++;
								if($image['Name'] == 'Floor plan'){
									$inner_arr[104] = $image['Uri1280'];
									continue;
								}
								
								$value_1_name				= skip_rem_name_cap($image['Name']);
								$value_1_captioin			= skip_rem_name_cap($image['Caption']);
								
								
								
								$value_1[$index]['Url']		= $image['Uri1280'];
								$value_1[$index]['Name']	= strlen($value_1_name)>3? $value_1_name:'';
								$value_1[$index]['Caption']	= strlen($value_1_captioin)>3? $value_1_captioin:'';
								
								if(limit_img and limit_img != false and $ix>=limit_img)
									unset($value_1[$index]);
							}
							$inner_arr[84] = $value_1;
							
						}
						elseif($key_1 == "Unit Details" or $key_1 == "Common Amenities" or $key_1 == "Amenities" or $key_1 == "Appliances" or $key_1 == "General Description"){							
							$arr = array();
							foreach($value_1 as $key_10 => $val_10) {								
								if($val_10[key($val_10)] === "No" ) continue;
								$value_10_54 = key($val_10);
								if($val_10[key($val_10)] != 1 and $val_10[key($val_10)] != "Yes")
									$value_10_54 .= ":" . $val_10[key($val_10)];
								$arr[] = $value_10_54;
							}
							$inner_arr[$keep_in_CSV[$key_1]] = $arr;
//	fwrite($LOG_handle, print_r( $inner_arr[46],true));
						}
						elseif ($key_1 == "HOA Info") {
							$value_hoa = array();
							foreach($value_1 as $val_23542356){
								$id = key($val_23542356);
								$value_hoa[$id] = $val_23542356[$id];
							}
							$dues = $value_hoa['Dues per month pesos'];
							unset($value_hoa['Dues per month pesos']);
							unset($value_hoa['HOA Name']);
							$includes = array();
							foreach($value_hoa as $key=>$value_6456){
								$includes[] = $value_6456!=1 ? $key . "-" . $value_6456 : $key;
							}
							$hoaInfo = array("dues"=> $dues);
							$hoaInfo["includes"] = !empty($includes)?implode(", ", $includes):'';
							$inner_arr[$keep_in_CSV[$key_1]] = $hoaInfo;
						}
						elseif($key_1 == "Contract Data"){
							if(isset($val_10) and is_array($val_10))
								$inner_arr[$keep_in_CSV[key($val_10)]] = $value_1[key($val_10)];
						}
					   	else{
							foreach($value_1 as $key_10 => $val_10) {
								if (key($val_10) == "Pet Friendly") {
									$inner_arr[$keep_in_CSV[key($val_10)]] = $val_10[key($val_10)] ;
								}
								else{
									if ($val_10[key($val_10)] === true) {
										$str_10 = $str_10 . key($val_10) . "; ";
									}
									else{
										$str_10 = $str_10 . key($val_10) . ":" . $val_10[key($val_10)] . ";";
									}
								}
							}
							
							$str_10 = substr($str_10 , 0, strlen($str_10) - 1);
							$str_10 = trim($str_10, ";");
							$inner_arr[$keep_in_CSV[$key_1]] = $str_10 ;
						}
					} 
					else{
						$inner_arr[$keep_in_CSV[$key_1]] = implode("; ",array_keys($value_1));						
					}
					
				}
				else{
					parse_JSON_string($value_1);
				}
			}
		}
	}
	return $inner_arr;
}		


/*
	Paramiters:
		Element:what

*/
function add_elemnt_to_dom($property ){
	global $xml_handle, $Headings, $my_listings_id,$propID;
	static $firstTime = true;
	if(!$property[1]) return ;
	//global $XML_doc, $root_elem, $propId, $sxe;
	$output = "<Property Id='{$property[1]}'>\n";
	
	$propID[$property[1]]	= $property[902];
	//echo $property[1];
	//print_r($property);
	$featured	= in_array($property[58], $my_listings_id);
	$output	.= "<featured>".(int)$featured."</featured>";
	foreach($property as $key=>$child){
		$key_header = array_search($key, $Headings);
		if($key_header == "" or ($firstTime == false and empty($child))) continue;
		$child = str_replace('N/A', '', $child);
		if(is_array($child)){
			if($key_header == "Photos"){
				$output .= "\t<Photos>\n";
					foreach($child as $key_1=>$child_1){
						$output .= "\t\t<img id='$key_1'>\n";
						$output	.= "\t\t\t<url>".($child_1['Url'])."</url>\n";
						$output	.= "\t\t\t<name>"._fix_text($child_1['Name'])."</name>\n";
						if($child_1['Caption'])
						$output	.= "\t\t\t<caption>"._fix_text($child_1['Caption'])."</caption>\n";
						$output	.= "\t\t</img>\n";
					}
				$output .= "\t</Photos>\n";
			}
			elseif ($key_header == "HOAInfo") {
				$output .= "\t<$key_header>";
					foreach($child as $key_454=>$value_364634){
						$output .= "\t\t<$key_454>";
							$output .= $value_364634;
						$output .= "\t\t</$key_454>\n";
					}
				$output .= "\t</$key_header>\n";
			}
			elseif( $key_header == "Water" or $key_header == "WaterSource" or $key_header == "Sewage" or $key_header == "Sewer") {
				$child = array_unique($child);
				$output .= "\t<$key_header>".implode(", ", $child)."</$key_header>\n";
			}
			else{					//if(array_search($key, $Headings) == "UnitDetails")
				$output .= "\t<$key_header>";
					$output .= serialize($child);
				$output .= "\t</$key_header>\n";
			}
		}
		else{
			$output .= "\t<$key_header>". _fix_text($child)."</$key_header>\n";
		}
		
	}
	$output .= "</Property>\n";
	fwrite($xml_handle, $output);
	if($firstTime == true){
		$firstTime = false;
	}
}



function _fix_text(&$text){
	$to_strip_XML = array("", "±", "''", );
	$text = str_replace($to_strip_XML , "" ,$text);
	$text = str_replace('a¢' , "-" ,$text);
	$text = remove_accents($text);
	$text = strip_tags($text);
	$text = htmlspecialchars($text);
	return $text ;//= iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
}


/**
 * Converts all accent characters to ASCII characters.
 *
 * If there are no accent characters, then the string given is just returned.
 *
 * @since 1.2.1
 *
 * @param string $string Text that might have accent characters
 * @return string Filtered string with replaced "nice" characters.
 */
function remove_accents($string) {
        if ( !preg_match('/[\x80-\xff]/', $string) )
                return $string;
        if (1==1) {
                $chars = array(
                // Decompositions for Latin-1 Supplement
                chr(194).chr(170) => 'a', chr(194).chr(186) => 'o',
                chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
                chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
                chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
                chr(195).chr(134) => 'AE',chr(195).chr(135) => 'C',
                chr(195).chr(136) => 'E', chr(195).chr(137) => 'E',
                chr(195).chr(138) => 'E', chr(195).chr(139) => 'E',
                chr(195).chr(140) => 'I', chr(195).chr(141) => 'I',
                chr(195).chr(142) => 'I', chr(195).chr(143) => 'I',
                chr(195).chr(144) => 'D', chr(195).chr(145) => 'N',
                chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
                chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
                chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
                chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
                chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
                chr(195).chr(158) => 'TH',chr(195).chr(159) => 's',
                chr(195).chr(160) => 'a', chr(195).chr(161) => 'a',
                chr(195).chr(162) => 'a', chr(195).chr(163) => 'a',
                chr(195).chr(164) => 'a', chr(195).chr(165) => 'a',
                chr(195).chr(166) => 'ae',chr(195).chr(167) => 'c',
                chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
                chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
                chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
                chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
                chr(195).chr(176) => 'd', chr(195).chr(177) => 'n',
                chr(195).chr(178) => 'o', chr(195).chr(179) => 'o',
                chr(195).chr(180) => 'o', chr(195).chr(181) => 'o',
                chr(195).chr(182) => 'o', chr(195).chr(184) => 'o',
                chr(195).chr(185) => 'u', chr(195).chr(186) => 'u',
                chr(195).chr(187) => 'u', chr(195).chr(188) => 'u',
                chr(195).chr(189) => 'y', chr(195).chr(190) => 'th',
                chr(195).chr(191) => 'y', chr(195).chr(152) => 'O',
                // Decompositions for Latin Extended-A
                chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
                chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
                chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
                chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
                chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
                chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
                chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
                chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
                chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
                chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
                chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
                chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
                chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
                chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
                chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
                chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
                chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
                chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
                chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
                chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
                chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
                chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
                chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
                chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
                chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
                chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
                chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
                chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
                chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
                chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
                chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
                chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
                chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
                chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
                chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
                chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
                chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
                chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
                chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
                chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
                chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
                chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
                chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
                chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
                chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
                chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
                chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
                chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
                chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
                chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
                chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
                chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
                chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
                chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
                chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
                chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
                chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
                chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
                chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
                chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
                chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
                chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
                chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
                chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
                // Decompositions for Latin Extended-B
                chr(200).chr(152) => 'S', chr(200).chr(153) => 's',
                chr(200).chr(154) => 'T', chr(200).chr(155) => 't',
                // Euro Sign
                chr(226).chr(130).chr(172) => 'E',
                // GBP (Pound) Sign
                chr(194).chr(163) => '',
                // Vowels with diacritic (Vietnamese)
                // unmarked
                chr(198).chr(160) => 'O', chr(198).chr(161) => 'o',
                chr(198).chr(175) => 'U', chr(198).chr(176) => 'u',
                // grave accent
                chr(225).chr(186).chr(166) => 'A', chr(225).chr(186).chr(167) => 'a',
                chr(225).chr(186).chr(176) => 'A', chr(225).chr(186).chr(177) => 'a',
                chr(225).chr(187).chr(128) => 'E', chr(225).chr(187).chr(129) => 'e',
                chr(225).chr(187).chr(146) => 'O', chr(225).chr(187).chr(147) => 'o',
                chr(225).chr(187).chr(156) => 'O', chr(225).chr(187).chr(157) => 'o',
                chr(225).chr(187).chr(170) => 'U', chr(225).chr(187).chr(171) => 'u',
                chr(225).chr(187).chr(178) => 'Y', chr(225).chr(187).chr(179) => 'y',
                // hook
                chr(225).chr(186).chr(162) => 'A', chr(225).chr(186).chr(163) => 'a',
                chr(225).chr(186).chr(168) => 'A', chr(225).chr(186).chr(169) => 'a',
                chr(225).chr(186).chr(178) => 'A', chr(225).chr(186).chr(179) => 'a',
                chr(225).chr(186).chr(186) => 'E', chr(225).chr(186).chr(187) => 'e',
                chr(225).chr(187).chr(130) => 'E', chr(225).chr(187).chr(131) => 'e',
                chr(225).chr(187).chr(136) => 'I', chr(225).chr(187).chr(137) => 'i',
                chr(225).chr(187).chr(142) => 'O', chr(225).chr(187).chr(143) => 'o',
                chr(225).chr(187).chr(148) => 'O', chr(225).chr(187).chr(149) => 'o',
                chr(225).chr(187).chr(158) => 'O', chr(225).chr(187).chr(159) => 'o',
                chr(225).chr(187).chr(166) => 'U', chr(225).chr(187).chr(167) => 'u',
                chr(225).chr(187).chr(172) => 'U', chr(225).chr(187).chr(173) => 'u',
                chr(225).chr(187).chr(182) => 'Y', chr(225).chr(187).chr(183) => 'y',
                // tilde
                chr(225).chr(186).chr(170) => 'A', chr(225).chr(186).chr(171) => 'a',
                chr(225).chr(186).chr(180) => 'A', chr(225).chr(186).chr(181) => 'a',
                chr(225).chr(186).chr(188) => 'E', chr(225).chr(186).chr(189) => 'e',
                chr(225).chr(187).chr(132) => 'E', chr(225).chr(187).chr(133) => 'e',
                chr(225).chr(187).chr(150) => 'O', chr(225).chr(187).chr(151) => 'o',
                chr(225).chr(187).chr(160) => 'O', chr(225).chr(187).chr(161) => 'o',
                chr(225).chr(187).chr(174) => 'U', chr(225).chr(187).chr(175) => 'u',
                chr(225).chr(187).chr(184) => 'Y', chr(225).chr(187).chr(185) => 'y',
                // acute accent
                chr(225).chr(186).chr(164) => 'A', chr(225).chr(186).chr(165) => 'a',
                chr(225).chr(186).chr(174) => 'A', chr(225).chr(186).chr(175) => 'a',
                chr(225).chr(186).chr(190) => 'E', chr(225).chr(186).chr(191) => 'e',
                chr(225).chr(187).chr(144) => 'O', chr(225).chr(187).chr(145) => 'o',
                chr(225).chr(187).chr(154) => 'O', chr(225).chr(187).chr(155) => 'o',
                chr(225).chr(187).chr(168) => 'U', chr(225).chr(187).chr(169) => 'u',
                // dot below
                chr(225).chr(186).chr(160) => 'A', chr(225).chr(186).chr(161) => 'a',
                chr(225).chr(186).chr(172) => 'A', chr(225).chr(186).chr(173) => 'a',
                chr(225).chr(186).chr(182) => 'A', chr(225).chr(186).chr(183) => 'a',
                chr(225).chr(186).chr(184) => 'E', chr(225).chr(186).chr(185) => 'e',
                chr(225).chr(187).chr(134) => 'E', chr(225).chr(187).chr(135) => 'e',
                chr(225).chr(187).chr(138) => 'I', chr(225).chr(187).chr(139) => 'i',
                chr(225).chr(187).chr(140) => 'O', chr(225).chr(187).chr(141) => 'o',
                chr(225).chr(187).chr(152) => 'O', chr(225).chr(187).chr(153) => 'o',
                chr(225).chr(187).chr(162) => 'O', chr(225).chr(187).chr(163) => 'o',
                chr(225).chr(187).chr(164) => 'U', chr(225).chr(187).chr(165) => 'u',
                chr(225).chr(187).chr(176) => 'U', chr(225).chr(187).chr(177) => 'u',
                chr(225).chr(187).chr(180) => 'Y', chr(225).chr(187).chr(181) => 'y',
                // Vowels with diacritic (Chinese, Hanyu Pinyin)
                chr(201).chr(145) => 'a',
                // macron
                chr(199).chr(149) => 'U', chr(199).chr(150) => 'u',
                // acute accent
                chr(199).chr(151) => 'U', chr(199).chr(152) => 'u',
                // caron
                chr(199).chr(141) => 'A', chr(199).chr(142) => 'a',
                chr(199).chr(143) => 'I', chr(199).chr(144) => 'i',
                chr(199).chr(145) => 'O', chr(199).chr(146) => 'o',
                chr(199).chr(147) => 'U', chr(199).chr(148) => 'u',
                chr(199).chr(153) => 'U', chr(199).chr(154) => 'u',
                // grave accent
                chr(199).chr(155) => 'U', chr(199).chr(156) => 'u',
                );
                // Used for locale-specific rules
                $locale = 'en_US';
                if ( 'de_DE' == $locale ) {
                        $chars[ chr(195).chr(132) ] = 'Ae';
                        $chars[ chr(195).chr(164) ] = 'ae';
                        $chars[ chr(195).chr(150) ] = 'Oe';
                        $chars[ chr(195).chr(182) ] = 'oe';
                        $chars[ chr(195).chr(156) ] = 'Ue';
                        $chars[ chr(195).chr(188) ] = 'ue';
                        $chars[ chr(195).chr(159) ] = 'ss';
                } elseif ( 'da_DK' === $locale ) {
                        $chars[ chr(195).chr(134) ] = 'Ae';
                        $chars[ chr(195).chr(166) ] = 'ae';
                        $chars[ chr(195).chr(152) ] = 'Oe';
                        $chars[ chr(195).chr(184) ] = 'oe';
                        $chars[ chr(195).chr(133) ] = 'Aa';
                        $chars[ chr(195).chr(165) ] = 'aa';
                }
                $string = strtr($string, $chars);
        } else {
                // Assume ISO-8859-1 if not UTF-8
                $chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
                        .chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
                        .chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
                        .chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
                        .chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
                        .chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
                        .chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
                        .chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
                        .chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
                        .chr(252).chr(253).chr(255);
                $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";
                $string = strtr($string, $chars['in'], $chars['out']);
                $double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
                $double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
                $string = str_replace($double_chars['in'], $double_chars['out'], $string);
        }
        return $string;
}


#-----------------------------------------------------------------------------------------

function get_video_id($url){
	if(strpos("/embed/", $url) != false){
		$arr_1 = explode("/embed/", $url);
		if(isset($arr_1[1]))
			$arr_2 = explode("?", $arr_1[1]);
		if(isset($arr_2[0]))
			return (string)$arr_2[0];
	}
	elseif(strpos("youtu.be/",$url) !== false){
		$arr_1 = explode("youtu.be/", $url);
		if(isset($arr_1[1]))
			$arr_2 = explode("?", $arr_1[1]);
		if(isset($arr_2[0]))
			return (string)$arr_2[0];
	}
	elseif(strpos("watch?v=",$url) !== false){
		$arr_1 = explode("watch?v=", $url);
		if(isset($arr_1[1]))
			$arr_2 = explode("?", $arr_1[1]);
		if(isset($arr_2[0]))
			return (string)$arr_2[0];
	}
	//return $url;
}




function check_if_already_exist($mod = 'build'){
	global $arr_listing_id, $result, $new_added, $existing_item_found;
	//$existing_item_found	= false;
	//echo '<pre>';
	//print_r($result);
	//echo '</pre>';
	if(count($result)){
		foreach($result as $listing){
			$__listingid		= $listing['Id'];
			$__ampi				= $listing['StandardFields']['ListingId'];
			$__last_mod_date	= $listing['StandardFields']['ModificationTimestamp'];
			$__added_date		= $listing['StandardFields']['OriginalOnMarketTimestamp'];
			
			//echo "$__ampi  ||  if(!array_key_exists($__listingid, \$arr_listing_id) or ($mod == 'update' and ";
			//if(isset($arr_listing_id[$__listingid]))
				//echo $arr_listing_id[$__listingid];
			//echo " != $__last_mod_date) )<br>\n";
			
			/*echo count($new_added);
			echo "<br />";
			echo MaxListings;
			echo "<br />";
			echo count($arr_listing_id);
			echo "<br />";*/
				if(count($new_added)<MaxListings || $mod == 'update'){
					if( !array_key_exists($__listingid, $arr_listing_id) or ($mod == 'update' and isset($arr_listing_id[$__listingid]) and $arr_listing_id[$__listingid][0] != $__last_mod_date ) ){
							if( debug )
								echo "\n New item found";
							$new_added[$__ampi]					= $__listingid;
							$arr_listing_id[$__listingid]		= array($__last_mod_date, $__ampi);
							
							$existing_item_found				= false;
						
					}
					else{
						
						if($mod != 'my' or $mod != 'build')
							$existing_item_found				+= 1;
					}
				}
			
		}
		array_filter($new_added);
	}	
	//echo "<br><br>\n\n" ;
}

function skip_rem_name_cap($name_cap){
	$skip_in_photo_name	= array('DSC','_MG_','IMG','DSCN',);
	if(is_numeric($name_cap))
		return '';
	
	$name_cap_1				= str_replace($skip_in_photo_name, '', $name_cap);
	if($name_cap != $name_cap_1)
		return '';
	
	$name_cap				= preg_replace("@\d{3,}@", '', $name_cap);
	
	//if(preg_match("@\d{4}@", $name_cap))
	//	return '';
	
	$name_cap				= str_replace(array('-','_'), ' ', $name_cap);
	return trim($name_cap, ' -*/');
	
}


function zm_get_my_litings(){
	global $api, $Parameters, $result, $existing_item_found;
	$existing_item_found = 0;
	$Parameters['_page'] = 1;
	$result = $api->GetMyListings($Parameters);
	populate_listings_id($result, true); // adding to variable $my_listings_id
	check_if_already_exist('my');
	
	for($i=2;$i<=$api->total_pages;$i++){
		$Parameters['_page'] = $i;
		$result = $api->GetMyListings($Parameters);
		populate_listings_id($result, true); // adding to variable $my_listings_id
		check_if_already_exist('my');
	}
	
}

function zm_get_listings($type	= '', $existing_item_found = 0){
	global $api, $Parameters, $result, $existing_item_found;
	$existing_item_found = 0;
	$Parameters['_page'] = 1;
	
	$result = $api->GetListings($Parameters);
	
	populate_listings_id($result);
	if($type != 'build' or !isset($_GET['update']))
		check_if_already_exist($type);
	
	for($i=2;$i<=$api->total_pages;$i++){
		$Parameters['_page']	= $i;
		$result					= $api->GetListings($Parameters);
		populate_listings_id($result);
		if($type != 'build' or !isset($_GET['update']))
			check_if_already_exist($type);
		//if($existing_item_found > 25)
		//	$api->total_pages = false;
		//if(MaxListings and $i>(MaxListings/25))
			//$api->total_pages = false;
	}
	
}