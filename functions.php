<?php
function Burn_Tokens($officialTokens) {
		$asset = $officialTokens["Token"]; //Original asset
		$voteToken = $officialTokens["Vote"]; //Original Vote
		echo "<h5> The Chain Gang Token: <a href='http://api.blockscan.com/api2?module=asset&action=holders&name=$asset'>$asset</a> </h5>";
		echo "<h5>Vote Token : <a href='http://api.blockscan.com/api2?module=asset&action=holders&name=$voteToken'>$voteToken</a> </h5>";

		echo " To become a candidate you must issue a numeric and an alphabet Counterparty asset to the holders of :

 <a href='http://api.blockscan.com/api2?module=asset&action=holders&name=$asset'>$asset</a>. " ;
echo " <br> The candidate with the most <a href='http://api.blockscan.com/api2?module=asset&action=holders&name=$voteToken'>$voteToken</a> will become the next Official Token";
echo " <br>";
echo "</div>";
echo "<h3> Candidates: </h3>";

		$candidates = Find_Candidates($asset); //Search through assets for match that is distributed to all asset holders and returns an array.
		//var_dump($candidates);
		$poll = [];
		foreach($candidates as $newCan)
			{ //creating burn addresses that contain Vote & Token Name.
			$voteToken = $newCan["Vote"];
			$canToken = $newCan["Token"];
			if (isset($voteToken) && isset($canToken)) {
			$validAddress = Burn_Prep($canToken, $voteToken);
			echo "<br> <h4>To vote for $canToken send your vour vote Tokens to: $validAddress </h4>";
			$poll[$validAddress] = array(
				"Vote" => $voteToken,
				"Token" => $canToken
			);
			}
 else { unset($newcan);
}}
		//var_dump($poll);
		if ($candidates == NULL)
			{
			echo "No viable Candidates! :(";
			//var_dump($officialTokens);
			die();
			}

		Vote($officialTokens, $poll);
	}
	
function Find_Candidates($asset)
	{
	$allowedIssuance = 5000;
	$minIssuance = $allowedIssuance/100;
	$assetDetail = 'http://api.blockscan.com/api2?module=asset&action=info&name=';
	$asset_info = json_decode(file_get_contents($assetDetail . $asset) , true);
	$chain_token_info = $asset_info['data'];
	$assetHolders = 'http://api.blockscan.com/api2?module=asset&action=holders&name=';
	$jarr = json_decode(file_get_contents($assetHolders . $asset) , true);
	$Holders_of_Chain_token = $jarr['data'];
	$totalCount = $jarr['totalcount'];
	$array_length_of_chain_token = count($Holders_of_Chain_token);
	$Other_Assets = 'http://api.blockscan.com/api2?module=address&action=balance&btc_address=';
	$store = array();
	for ($i = 0; $i < $array_length_of_chain_token; ++$i)
		{
		$balance = $Holders_of_Chain_token[$i]['balance'];
		$compare = json_decode(file_get_contents($Other_Assets . $Holders_of_Chain_token[$i]['address']) , true);
		$arraylength = count(json_decode(file_get_contents($Other_Assets . $Holders_of_Chain_token[$i]['address']) , true) ['data']);
		for ($x = 0; $x < $arraylength; ++$x)
			{
			$compare_data = $compare['data'];
			$balances = $compare_data[$x]['balance'];
			$assets = $compare_data[$x]['asset'];
			if (($asset != $assets) & ($balances >= $balance))
				{
				$store[] = $assets;
				}
			}
		}

	$c = array_count_values($store);
	$var = array_keys($c, $array_length_of_chain_token);

	// echo var_dump($var);

	$viableAssets = Array();
	foreach($var as $thing)
		{
		$testing = $chain_token_info[0];
		$new = json_decode(file_get_contents($assetDetail . $thing) , true);
		$new_data = $new['data'];
		$the_data = $new_data[0];
		$circulation = $the_data["circulation"];
		$issuer = $the_data["issuer"];
		if (strcspn($thing, '0123456789') != strlen($thing))
			{
			$Vote = true;
			}
		  else
			{
			$Vote = false;
			}

		if ($testing["circulation"] + $allowedIssuance >= $circulation && $circulation >= $testing["circulation"] + $minIssuance )
			{
			if ($the_data["locked"] == "False")
				{
				//echo "<br /> WARNING! THIS ASSET: ( $thing ) IS NOT LOCKED <br />";
				}

			else if ($Vote == false)
				{
				$viableAssets["$issuer"]["Token"] = $thing;
				echo "<ul> asset stats ";
					foreach($new_data[0] as $key => $val)
						{
				echo "<li> $key : $val </li> ";
				
				}
				echo "</ul>";
					
				}
			elseif ($Vote == true)
				{
				$viableAssets["$issuer"]["Vote"] = $thing;
				}

			// var_dump($viableAssets);

			
			}
		  else
			{
			//echo "<br />";
			//echo "<br /> fail $thing does not work " . $the_data["circulation"] . " and " . $testing["circulation"] . "<br /> Locked state is : " . $the_data["locked"] . "<br />";
			}
		}

	$tester = $viableAssets;
	foreach ($tester as $inner) {
	echo "<ul> Pair";
    //  Check type
    if (is_array($inner)) {
        //  Scan through inner loop
        foreach ($inner as $key => $value) {
           echo "<li> $key : $value </li> ";
           
        }
    }
}
	
					/**/
	//var_dump($tester);
	echo "</ul>";
	return $tester;
	};
	
function Burn_Prep($token,$vote){
		$voteNumber = substr($vote, 1);
		$burn = "1".$token . $voteNumber . "xxxxx";
		while (strlen($burn) < 33) {
			$burn = $burn."x";
		}
		$validAddress = burn_address($burn);
		return $validAddress;
	}

?>
