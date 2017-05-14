<?php
ini_set('display_errors',1); // for the development PC only
error_reporting(E_ALL); // ALWAYS

function my_comp($a, $b)
{
	return ((int)explode(",",$b)[1] - (int)explode(",",$a)[1]);
}

$lines = array();
$scores = fopen("scores.txt", "r") or die("Unable to open file!");

# read file and put in an array
$j = 0;
while (!feof($scores)) {
	$line = fgets($scores);
	if (!feof($scores)) {
		$lines[ ] = $line;
		$j = $j + 1;
	}
}

fclose($scores);

$name     = $_POST['name'];
$score    = $_POST['score'];


$entry	  = "".$name.",".$score;
$lines[ ] = "".$entry."\n";

usort($lines, "my_comp");

for($i = 0; $i < count($lines); $i++) {
	echo "<p>".($i + 1).") ".$lines[$i]."<br></p>";
}

$scores = fopen("scores.txt", "w") or die("Unable to open file!");
for($i = 0; $i < count($lines); $i++) {
	fwrite($scores, $lines[$i]);
}
fclose($scores);


?>
