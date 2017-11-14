<?php
//setting the max time limit to 5min 
ini_set('max_execution_time', 300);

//adding phpDom to fetch data 
include("simple_html_dom.php");
//2 to 1069
//8069
$ShopNum = 989; 
$shop_name = array();
$shop_address = array();
$shop_category = array();
//fetching data 	
//for($i = 214; $i <= 811; $i++){
$i = 491;
	for($j =0; $j<=40; $j+=10){
	$base = 'http://www.yangondirectory.com/en/categories-index/search.html?searchword=&Itemid=210&option=com_mtree&task=search&skip_township=0&skip_cat=0&cat_id='.$i.'&start='.$j;

//& start = 10 
//establishing connection with the site 
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_URL, $base);
	curl_setopt($curl, CURLOPT_REFERER, $base);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	$str = curl_exec($curl);
	curl_close($curl);

// Create a DOM object
	$html_base = new simple_html_dom();
// Load HTML from a string
	$html_base->load($str);

//adding the shop names into an array

	foreach($html_base->find('a.first-feature-tag-title') as $element) {
		array_push($shop_name, $element->innertext);
	}

//adding address into an array
	
	foreach($html_base->find('div[class="address blk-margin"]') as $element) {
		array_push($shop_address, $element->plaintext);
	}

//adding category into an array

	foreach($html_base->find('div[class="category blk-margin"]') as $element) {
		array_push($shop_category, $element->plaintext);
	}

//final shop array
// 	$shop = array($shop_name, $shop_address, $shop_category);
// print "<pre>";
// print_r($shop);
// print "</pre>";
$html_base->clear(); 
	unset($html_base);
}
//}
	


//connecting to database 
	$servername = "localhost";
	$username = "root";
	$password = null;
	$dbname = "blipdb";
	try {

		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$sql = "INSERT INTO yellow_directory (Num, ShopName, Address, Category)
				VALUES ( :ShopNum, :ShopName, :Address, :Category)";
		$stmt = $conn -> prepare($sql);
		$stmt-> bindParam(':ShopNum', $ShopNum);
		$stmt-> bindParam(':ShopName', $ShopName);
		$stmt-> bindParam(':Address', $Address);
		$stmt-> bindParam(':Category', $Category);

    //inserting the data 
		for($i=0; $i<count($shop_name);$i++){
			$ShopNum++;
			$ShopName = $shop_name[$i];
			$Address = $shop_address[$i];
			$Category = $shop_category[$i];

			$stmt->execute();
		}
		echo "New record created successfully";
	
	}
	catch(PDOException $e)
	{
		echo "Data can't be added!" . "<br>" . $e->getMessage();
	}

	$conn = null;


?>


