<?php
session_start();
$times = 100000;

$path = 'http://localhost';


# ==================================
$start_time_1 = microtime(TRUE);
for ($x=0; $x < $times ; $x++) { 
    # Code Start
    $code = substr($path, 0,-1)=='/' ? $path : $path . '/';

}
$end_time_1 = microtime(TRUE);
# ==================================


# ==================================
$start_time_2 = microtime(TRUE);
for ($x=0; $x < $times ; $x++) { 
    # Code Start
    $code = rtrim($path, '/') . '/';

}
$end_time_2 = microtime(TRUE);
# ==================================

$result_1 = ($end_time_1 - $start_time_1);
echo 'Code_1 took: ' . $result_1;

echo '<br /><br />';

$result_2 = ($end_time_2 - $start_time_2);
echo 'Code_2 took: ' . $result_2;

echo '<hr>';
if ( $result_1 < $result_2 ) {
	echo 'Code_1 is <strong>' . sprintf('%.2f', ( 100 - ($result_1*100) / $result_2 )) . '%</strong> faster than Code_2';
} else if ( $result_2 < $result_1 ) {
	echo 'Code_2 is <strong>' . sprintf('%.2f', ( 100 - ($result_2*100) / $result_1 )) . '%</strong> faster than Code_1';
} else {
	echo 'Seems to be equal percentage';
}

// mySashka =p

// Initiate array if not set
if ( !isset($_SESSION['code_1'], $_SESSION['code_1'], $_SESSION['time']) )
    $_SESSION['code_1'] = $_SESSION['code_2'] = $_SESSION['time'] = 0;

// Initiate md5-checksum of profiling code
if ( !isset($_SESSION['md5_1'], $_SESSION['md5_2']) )
    $_SESSION['md5_1'] = $_SESSION['md5_2'] = '';

// Reset counts when code change
$profile_code = array_map( 'md5', findLine( '# Code Start', token_get_all(file_get_contents(__FILE__)), 2 ) ); // MD5
if ( $_SESSION['md5_1'] != $profile_code[0] || $_SESSION['md5_2'] != $profile_code[1] || $_SESSION['time'] != $times ) {
    $_SESSION['md5_1'] = $profile_code[0];
    $_SESSION['md5_2'] = $profile_code[1];
    $_SESSION['code_1'] = $_SESSION['code_2'] = 0;
    $_SESSION['time'] = $times;
}

if ( $result_1 < $result_2)
    $_SESSION['code_1']++;
else if ( $result_2 < $result_1)
    $_SESSION['code_2']++;

echo '<br /><hr>';
echo 'Code_1: <span style="color:' . ($_SESSION['code_1']>$_SESSION['code_2']?'green':'red') . '">' . $_SESSION['code_1'] . '</span> | ';
echo 'Code_2: <span style="color:' . ($_SESSION['code_1']<$_SESSION['code_2']?'green':'red') . '">' . $_SESSION['code_2'] . '</span>';

/* Find line by providing variable name
 * @param: variable defined above
 * @param: array list of codes
 * @param: count should be exactly to this
 */
function findLine($needle, $array, $count = 2, $type = '370') {
    $codes = array();
    foreach ($array as $key => $value) {
        if ( is_array($value) && trim($value[1]) == $needle && $value[0] == $type ) {
            $function_line = (int)$key+1;
            $codes[] = readCode($function_line, $array);
        }
    }

    if ( $count !== count($codes) )
        exit('Count doesnot ended up as expected, found ' . count($codes) .' when it should be ' . $count . '.');
    else
        return $codes;
}

// Return all code of it.
function readCode($line, $array) {
    if ( isset($array[ $line ]) ) {

      $code_str = '';
      $braces = 1;
      for ($x=$line; $x < count($array); $x++) {
        if (!isset($array[$x])) continue;

        if ( $array[$x] == '{' ) $braces++;

        if ( $array[$x] == '}' ) {
            $braces--;
            if ( $braces == 0 ) break;
        }

        if ( is_array($array[$x]) )
            $code_str .= $array[$x][1];
        else
            $code_str .= $array[$x];
      }

      return trim($code_str);
    }
}
