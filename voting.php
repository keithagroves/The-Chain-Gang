<?php
function Catch_up($officialTokens, $poll)
	{ // Catch up with chain
		
	$blockCount = 'http://api.blockscan.com/api2?module=proxy&action=get_running_info'; //Block info link.
	$assetDetail = 'http://api.blockscan.com/api2?module=asset&action=info&name=';
	$assetHolders = 'http://api.blockscan.com/api2?module=asset&action=holders&name=';
	$blocksApart = 1008; // one week
	$checkBlock = json_decode(file_get_contents($blockCount) , true) ["bitcoin_block_count"];
	//$countBlock = floor($endBlock / $blocksApart); //how far along the Token chain we should be.
	$tokenChainCount = $poll;
	$voteToken = $officialTokens["Vote"];
	$voteResults = json_decode(file_get_contents($assetHolders . $voteToken) , true) ["data"]; //gets a list of vote asset holders
	$startBlock = (340000 - 4000) + ($poll * $blocksApart); //starting block reference.
	$endBlock = $checkBlock - $startBlock; //distance gone in blocks from starting point.
	echo "<Br> This is the ver start $startBlock";
	//var_dump($voteResults);
	echo "<br> and this is endblock/blocks apart ". (100/($endBlock / $blocksApart)) ;
	

	$countVoteArray = count($voteResults);
	for ($i = 0; $i < $countVoteArray; ++$i)
		{
		$votePercent = $voteResults[$i]["percentage"];
		echo "<br /> Vote Percent $votePercent";
		$voteAddress = $voteResults[$i]["address"];
		echo "<br /> Vote Address $voteAddress";
		if ($votePercent >= (100 / ($endBlock / $blocksApart))) {
			$vote = substr($voteAddress, 7, -7);
			echo "<br> vote: $vote";
			$token = substr($voteAddress, 1, 6);
			echo "<br> vote: $token";
			$vote = str_replace("o", 0, $vote);
			$voteCheck = json_decode(file_get_contents($assetDetail . $vote) , true) ["data"];
			var_dump($voteCheck);
			$tokenCheck = json_decode(file_get_contents($assetDetail . $token) , true) ["data"];
			var_dump($tokenCheck);
			if ($tokenCheck == null || $voteCheck == null)
				{
				echo "<br /> no good <br />";
				return $officialTokens;
				}
			  else
				{
				$checkTokens = array(
					"Token" => $token,
					"Vote" => $vote
				);
				var_dump($checkTokens);
				return $checkTokens;
				}
			}
		}}
		
	function Vote($officialTokens, $poll)
	{ // Vote Counter


	$assetDetail = 'http://api.blockscan.com/api2?module=asset&action=info&name=';
	$assetHolders = 'http://api.blockscan.com/api2?module=asset&action=holders&name=';
	$voteToken = $officialTokens["Vote"];
	$voteResults = json_decode(file_get_contents($assetHolders . $voteToken) , true) ["data"]; //gets a list of vote asset holders

	//var_dump($voteResults);

	$countVoteArray = count($voteResults);
	for ($i = 0; $i < $countVoteArray; ++$i)
		{
		$votePercent = $voteResults[$i]["percentage"];
		echo "<br /> $votePercent";
		$voteAddress = $voteResults[$i]["address"];
		echo "<br /> $voteAddress";
		
		if ($poll[$voteAddress] == null)
			{
			echo " THIS IS NOT A CANDIDATE! ";
			}
		elseif ($votePercent >= (100 / ($endBlock / $blocksApart)))
			{
			$officialTokens = $poll[$voteAddress];
			return $officialTokens;
			}
		  else
			{
			echo " Candidate does not have enough votes ";
			}
		
}
}


?>
