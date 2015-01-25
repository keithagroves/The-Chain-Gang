<?php
function Find_Candidates($asset)
	{
	$allowedIssuance = 50;
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

		if ($testing["circulation"] + $allowedIssuance == $circulation)
			{
			if ($the_data["locked"] == "False")
				{
				echo "<br /> WARNING! THIS ASSET: ( $thing ) IS NOT LOCKED <br />";
				}

			if ($Vote == false)
				{
				$viableAssets["$issuer"]["Token"] = $thing;
				}
			elseif ($Vote == true)
				{
				$viableAssets["$issuer"]["Vote"] = $thing;
				}

			// var_dump($viableAssets);

			echo "<br /> Candidate! </br>";
			foreach($new_data[0] as $key => $val)
				{
				echo "<br /> $key : $val ";
				}
			}
		  else
			{
			echo "<br />";
			echo "<br /> fail $thing does not work " . $the_data["circulation"] . " and " . $testing["circulation"] . "<br /> Locked state is : " . $the_data["locked"] . "<br />";
			}
		}

	$tester = $viableAssets;
	var_dump($tester);
	return $tester;
	};
	
function Burn_Tokens($officialTokens) {
		$asset = $officialTokens["Token"]; //Original asset
		$voteToken = $officialTokens["Vote"]; //Original Vote
		echo "<br /> The Chain Gang Token: $asset </br>";
		echo "<br /> Vote Token : $voteToken</br>";
		$candidates = Find_Candidates($asset); //Search through assets for match that is distributed to all asset holders and returns an array.
		var_dump($candidates);
		$poll = [];
		foreach($candidates as $newCan)
			{ //creating burn addresses that contain Vote & Token Name.
			$voteToken = $newCan["Vote"];
			$canToken = $newCan["Token"];
			echo "<br> vote length".strlen($voteToken). " </br>";
			echo "<br> can length".strlen($canToken). " </br>";
			
			$Burn = "1".$canToken . $voteToken . "xxxxxxx";
			echo "<br> sting length". strlen($Burn);
			$validAddress = burn_address($Burn);
			echo "<br /> $canToken Vote Address : $validAddress   ";
			$poll[$validAddress] = array(
				"Vote" => $voteToken,
				"Token" => $canToken
			);
			}

		var_dump($poll);
		if ($candidates == NULL)
			{
			echo "here's where is dies";
			var_dump($officialTokens);
			die();
			}

		Vote($officialTokens, $poll);
	}
?>
