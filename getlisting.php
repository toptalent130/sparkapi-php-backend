<?php
define('debug', 0);
		
		
require_once("lib/Core.php");
require_once("_initialization.php");
require_once("_functions.php");

// define('MaxListings', 5, true);   //Change 10 to false after testing

/*-------------------------- from main file -------------------------------------------*/
$api = new SparkAPI_APIAuth("pvr_elements_key_2", "DGoBsf14TNypWPWEGj8E_");
//$api->SetDeveloperMode(true);
$api->SetApplicationName("Elements-Realty-Group/1.1");

$result = $api->Authenticate();


if ($result === false) {
	echo "API Error Code: {$api->last_error_code}<br>\n";
	echo "API Error Message: {$api->last_error_mess}<br>\n";
	exit;
}
$id="20210521145342963058000000";
if(isset($_GET["id"])){
$id=$_GET["id"];
}

echo "<br />";
// $result = $api->GetListing(
// 	array(
// 		"_expand"	=> "CustomFields,Photos,Rooms,Supplement,Units,Videos,VirtualTours,Documents",
// 	)
// );
$result = $api->GetListings(
	array(
		'_pagination' => 1,
		'_limit' => 1,
		'_page' => 2,
		'_filter' => "PropertyType Eq 'A'",
		'_expand' => 'CustomFields,Supplement,Documents'
	)
);
echo "<pre>";

print_r($result);
//do_optimization($result);
//print_r($inner_arr);
//add_elemnt_to_dom($inner_arr);
/*foreach($result as $key=>$value){
	
	if(is_array($value)){
		echo "<div>".$key." : Array Data Level 1 </div>";
		foreach($value as $index=>$item){
			if(is_array($item)){
				
				echo "<div style='margin-left:20px;'>".$index." : Array Data Level 2 </div>";
				foreach($item as $index1=>$item1){
					
					if(is_array($item1)){
					
						echo "<div style='margin-left:40px;'>".$index1." : Array Data Level 3 </div>";
						foreach($item1 as $index2=>$item2){
							
							if(is_array($item2)){					
								echo "<div style='margin-left:60px;'>".$index2." : Array Data Level 4 </div>";
								
								foreach($item2 as $index3=>$item3){
								echo "<div style='margin-left:80px;'>".$index3." : ".$item3."</div>";
								}
								
							
							}else{
							echo "<div style='margin-left:60px;'>".$index2." : ".$item2."</div>";
							}
							
							
						}
					
					
					}else{
						echo "<div style='margin-left:40px;'>".$index1." : ".$item1."</div>";
					}
				
				}
			
			}else{
			
				echo "<div style='margin-left:20px;'>".$index." : ".$item."</div>";
			}	
		}
	
	}else{
		echo "<div>". $key." : ".$value."</div>";
	}
	



}*/