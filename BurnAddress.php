<?php

function burn_address ($Assets) {


$base58_O = array(0,O);

$OString = str_replace ($base58_O,"o",$Assets); //replace base58 unfriendly characters
$newString= str_replace ("I","i",$OString);

echo $newString;

$Some1 = bc_base58_decode ($newString);

$Some2 = bc_dechex($Some1);

$Text = "000"  . substr($Some2, 0,39);
echo "<br> step 3 : $Text  ";
$Some3 = hexStringToByteString($Text);



$Some4 = hash("sha256",$Some3);

echo "<br> step4 : $Some4";

$Some5 =hash("sha256",hexStringToByteString($Some4));

echo "<br> step 5 : $Some5 <br> ";

$Some6 = substr($Some5,0,8);

$Some7 = $Text.$Some6;

echo "<br> step 6: $Some7</br>";

$Some8 ="1".bc_base58_encode(bc_hexdec($Some7));
echo "New Valid Burn Address ".$Some8."<br><br>";

return $Some8;
}


// base conversion is from hex to base58 via decimal. 
// Leading hex zero converts to 1 in base58 but it is dropped
// in the intermediate decimal stage.  Simply added back manually.


function hexStringToByteString($hexString){
    $len=strlen($hexString);

    $byteString="";
    for ($i=0;$i<$len;$i=$i+2){
        $charnum=hexdec(substr($hexString,$i,2));
        $byteString.=chr($charnum);
    }

return $byteString;
}
// BCmath version for huge numbers
function bc_arb_encode($num, $basestr) {
    if( ! function_exists('bcadd') ) {
        Throw new Exception('You need the BCmath extension.');
    }

    $base = strlen($basestr);
    $rep = '';

    while( true ){
        if( strlen($num) < 2 ) {
            if( intval($num) <= 0 ) {
                break;
            }
        }
        $rem = bcmod($num, $base);
        $rep = $basestr[intval($rem)] . $rep;
        $num = bcdiv(bcsub($num, $rem), $base);
    }
    return $rep;
}

function bc_arb_decode($num, $basestr) {
    if( ! function_exists('bcadd') ) {
        Throw new Exception('You need the BCmath extension.');
    }

    $base = strlen($basestr);
    $dec = '0';

    $num_arr = str_split((string)$num);
    $cnt = strlen($num);
    for($i=0; $i < $cnt; $i++) {
        $pos = strpos($basestr, $num_arr[$i]);
        if( $pos === false ) {
            Throw new Exception(sprintf('Unknown character %s at offset %d', $num_arr[$i], $i));
        }
        $dec = bcadd(bcmul($dec, $base), $pos);
    }
    return $dec;
}


// base 58 alias
function bc_base58_encode($num) {   
    return bc_arb_encode($num, '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
}
function bc_base58_decode($num) {
    return bc_arb_decode($num, '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
}

//hexdec with BCmath
function bc_hexdec($num) {
    return bc_arb_decode(strtolower($num), '0123456789abcdef');
}
function bc_dechex($num) {
    return bc_arb_encode($num, '0123456789abcdef');
}
?>
