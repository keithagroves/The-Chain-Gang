<?php
function Catch_up($officialTokens, $poll, $blocksApart,$startBlock)
	{ // Catch up with chain
		
	
	require_once ("./BurnAddress.php");
	require_once ("./functions.php");

	$blockCount = 'http://api.blockscan.com/api2?module=proxy&action=get_running_info'; //Block info link.
	$assetDetail = 'http://api.blockscan.com/api2?module=asset&action=info&name=';
	$assetHolders = 'http://api.blockscan.com/api2?module=asset&action=holders&name=';
	//$blocksApart = 1008; // one week
	$checkBlock = json_decode(file_get_contents($blockCount) , true) ["bitcoin_block_count"];
	//$countBlock = floor($endBlock / $blocksApart); //how far along the Token chain we should be.
	$tokenChainCount = $poll;
	$voteToken = $officialTokens["Vote"];
	$voteResults = json_decode(file_get_contents($assetHolders . $voteToken) , true) ["data"]; //gets a list of vote asset holders
	$smartBlock = $startBlock + ($poll * $blocksApart); //starting block reference.
	$endBlock = $checkBlock - $smartBlock; //distance gone in blocks from starting point.
	echo "<Br>  Reference $smartBlock";
	//var_dump($voteResults);
	$voteEquation = (100/($endBlock / $blocksApart));
	echo "<br> Percentage Vote Required ". round($voteEquation,2) . "%" ;
	
	$countVoteArray = count($voteResults);
	for ($i = 0; $i < $countVoteArray; ++$i)
		{
		$votePercent = $voteResults[$i]["percentage"];
		echo "<br /> Vote Percent $votePercent";
		$voteAddress = $voteResults[$i]["address"];
		echo "<br /> Vote Address $voteAddress";
		if ($votePercent >= (100 / ($endBlock / $blocksApart))) {
			$vote = Extract_Vote($voteAddress);
			$token = Extract_Token($voteAddress);
			echo "here vote address looks like this : $voteAddress";
			//burn_address($voteAddress);
				echo "<br> vote: $vote";
				echo "<br> token: $token does it still have that A?";
				$voteCheck = json_decode(file_get_contents($assetDetail . $vote) , true) ["data"];
				//var_dump($voteCheck);
				$tokenCheck = json_decode(file_get_contents($assetDetail . $token) , true) ["data"];
				//var_dump($tokenCheck);
					if ($tokenCheck == null || $voteCheck == null)
						{
				echo "<br /> Asset check returned null <br />";
				//$officialTokens;
				$prepare = Burn_Prep($token,$vote);
				echo "this is prepare : $prepare";
				} elseif (Burn_Prep($token,$vote) != $voteAddress){
						echo "here is the checksum vs the vote address " .Burn_Prep($token,$vote). " & ". $voteAddress;
						echo "checksum does not match for $token!";
						
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
			} else{
				echo "<br> Not enough votes <br>";
			}
		}
		return $officialTokens;
		}
		
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
		echo "<br /> Vote percent: $votePercent <br>";
		$voteAddress = $voteResults[$i]["address"];
		echo "<br /> Vote Address: $voteAddress <br>";
		
		if ($poll[$voteAddress] == null)
			{
			echo "<br> This address does not show up in the candidate search.";
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

function Extract_Vote ($voteAddress) 
{
$voteAddress = substr($voteAddress,1,25);
$vote = strpbrk($voteAddress, 0123456789);
$vote = "A". str_replace("o", 0, $vote);
$vote = "A".preg_replace("/[^0-9,.]/", "", $vote);
return $vote;
}

function Extract_Token ($voteAddress) 
{
$voteAddress = substr($voteAddress,1,25);
$r = my_offset($voteAddress);
$token = substr($voteAddress,0,$r);
return $token;
}

function my_offset($text) {
    preg_match('/\d/', $text, $m, PREG_OFFSET_CAPTURE);
    if (sizeof($m))
        return $m[0][1]; // 24 in your example

    // return anything you need for the case when there's no numbers in the string
    return strlen($text);
}
		

?>


 
