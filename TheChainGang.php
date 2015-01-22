<?php

$assetHolders = 'http://api.blockscan.com/api2?module=asset&action=holders&name='; //Holders of an asset
$blockCount = 'http://api.blockscan.com/api2?module=proxy&action=get_running_info'; //Block info link.


$blocksApart = 1008; //this makes the action perform every so many Bitcoin blocks. 144 = day, 1008 = week.
echo "This will execute every $blocksApart Blocks</br>";

$checkblock = json_decode(file_get_contents($blockCount),true)["bitcoin_block_count"];
echo "<br> current block count : $checkblock <br>";

$officialTokens = array("Token" => "TheChainGang", "Vote" => "A11035732284319710000"); // Original Official Tokens 

$startBlock = 340000 -1008; //starting block reference.


$endBlock = $checkblock - $startBlock; //distance gone in blocks from starting point.

$countblock = floor($endBlock / $blocksApart); 

echo "<br> </br.> iterations : $countblock </br> ";

if($countblock >= 1) {
    for($x = 0; $x < $countblock; ++$x) {
        $asset     = $officialTokens["Token"]; //Original asset
        $voteToken = $officialTokens["Vote"]; //Original Vote
        
        echo "<br> The Chain Gang Token: $asset </br>";
        echo "<br> Vote Token : $voteToken</br>";
        $candidates = Asset ($asset); //Search through assets for match that is distributed to all asset holders and returns an array.
        var_dump($candidates);
        if ($candidates == NULL){
            echo "here's where is dies";
            var_dump($officialTokens);
            die();
        }
			//Vote Counter
            $voteResults = json_decode(file_get_contents($assetHolders.$voteToken),true)["data"]; //gets a list of vote asset holders
            $countVoteArray = count($voteResults);
            echo "<br> countvoteArray = $countVoteArray ";
            echo "<br> This my equation: (100 / ($endBlock / $blocksApart))  =";
            for($i = 0; $i < $countVoteArray; ++$i) {
            $votePercent = $voteResults[$i]["percentage"];
            echo "<br> vote: $votePercent </br>";
            $voteAddress = $voteResults[$i]["address"];
            echo "<br> vote address : $voteAddress  <br>";
            echo "<br> vote required = ". (100 / ($endBlock / $blocksApart))."<br>";
            echo "actual Vote = $votePercent";
            if ($candidates[$voteAddress] == null) {
                echo " THIS IS NOT A CANDIDATE! ";
            }
            elseif ($votePercent >= (100 / ($endBlock / $blocksApart)))
            {
                $officialTokens = $candidates[$voteAddress];
                }
                else {
                    echo " Candidate does not have enough votes ";
                }}
}
}else {
    echo "<br>". ( $checkblock - $startBlock) ." Blocks Until Start ";
}


echo "<br> Official Token : ". $officialTokens["Token"];

echo "<br> Next Vote Token : ". $officialTokens['Vote'];

function Asset($asset)
{

     $allowedIssuance = 50;
     $assetDetail = 'http://api.blockscan.com/api2?module=asset&action=info&name=';
     $asset_info = json_decode(file_get_contents($assetDetail.$asset),true);
     $chain_token_info = $asset_info['data'];
     $assetHolders = 'http://api.blockscan.com/api2?module=asset&action=holders&name=';
     $jarr = json_decode(file_get_contents($assetHolders.$asset),true);
     $Holders_of_Chain_token = $jarr['data'];
     $totalCount = $jarr['totalcount'];
     $array_length_of_chain_token = count($Holders_of_Chain_token);
     $Other_Assets = 'http://api.blockscan.com/api2?module=address&action=balance&btc_address=';
     $store = array();
     for($i = 0; $i < $array_length_of_chain_token; ++$i) {
    
    
     $balance = $Holders_of_Chain_token[$i]['balance'];
     $compare = json_decode(file_get_contents($Other_Assets.$Holders_of_Chain_token[$i]['address']),true);
     $arraylength = count(json_decode(file_get_contents($Other_Assets.$Holders_of_Chain_token[$i]['address']),true)['data']);
        for($x = 0; $x < $arraylength; ++$x) {
            $compare_data = $compare['data'];
            $balances = $compare_data[$x]['balance'];
            $assets = $compare_data[$x]['asset'];
            if (($asset != $assets) & ($balances >= $balance)) {  
                    $store[] = $assets;
                }
            }}

    $c = array_count_values($store);
    $var = array_keys($c,$array_length_of_chain_token); 
    //echo var_dump($var);
    $viableAssets = Array();
    foreach ($var as $thing){
        $testing = $chain_token_info[0];
        $new = json_decode(file_get_contents($assetDetail.$thing),true);
        $new_data = $new['data'];
        $the_data = $new_data[0];
        $circulation = $the_data["circulation"];
        $issuer = $the_data["issuer"];
        if (strcspn($thing, '0123456789') != strlen($thing)){
        $Vote = true;
        }else{
        $Vote = false;
        }
        if ($testing["circulation"] + $allowedIssuance == $circulation) {
            if ($the_data["locked"] == "False") {
                echo "<br> WARNING! THIS ASSET: ( $thing ) IS NOT LOCKED <br>";
            }
            if ($Vote == false){
            $viableAssets["$issuer"]["Token"] =$thing;
        }
        elseif ($Vote == true) {
            $viableAssets["$issuer"]["Vote"] =$thing;
        }
            //var_dump($viableAssets);
            echo "<br> Candidate! </br>";
            
            foreach ($new_data[0] as $key => $val) {
                echo "<br> $key : $val ";
                
                
}
}

else {
    echo "<br>";
        echo "<br> fail $thing does not work " . $the_data["circulation"]. " and " . $testing["circulation"]. "<br> Locked state is : ". $the_data["locked"]. "<br>" ;
        
        
    }


}
        
            $tester = $viableAssets;
            var_dump($tester);
            return $tester;

            
 };

?>
