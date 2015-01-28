<?php
require_once ("./BurnAddress.php");
require_once ("./voting.php");
require_once ("./functions.php");

$assetHolders = 'http://api.blockscan.com/api2?module=asset&action=holders&name='; //Holders of an asset
$blockCount = 'http://api.blockscan.com/api2?module=proxy&action=get_running_info'; //Block info link.
$blocksApart = 1008; //this makes the action perform every so many Bitcoin blocks. 144 = day, 1008 = week.
echo "This will execute every $blocksApart Blocks</br>";
$checkBlock = json_decode(file_get_contents($blockCount) , true) ["bitcoin_block_count"];
echo "<br /> current block count : $checkBlock <br />";

//$officialTokens = array("Token" => "TXooooo", "Vote" => "A4330178176633399300"); // Original Official Tokens

//$tokenChainCount = //how many times can the vote token be traced to a viable address.
// The vote difficuly adjust according to how far we should be along.

$officialTokens = array(
"Token" => "TheChainGang",
"Vote" => "A11035732284319710000"
);

$startBlock = (340000); //starting block reference. Also in voting.php
$endBlock = ($checkBlock - $startBlock); //distance gone in blocks from starting point.
$countBlock = floor($endBlock / $blocksApart);
echo "<br /> </br.> iterations : $countBlock </br> ";
if ($countBlock == 0)
{
	echo "<br />" . ($blocksApart - ($checkBlock - $startBlock)) . " Blocks Until Start ";
	}
	for ($x = 0; $x < $countBlock; ++$x)
		{
		$oldToken = $officialTokens;
		$officialTokens = Catch_up($officialTokens,$x,$blocksApart, $startBlock);
		echo Catch_up($officialTokens,$x,$blocksApart, $startBlock);
		if ($officialTokens == $oldToken) {
		
		$x = $countBlock;
		}
}
		
echo Burn_Tokens($officialTokens);

echo "<br /> Official Token : " . $officialTokens["Token"];
echo "<br /> Next Vote Token : " . $officialTokens['Vote'];


?>
