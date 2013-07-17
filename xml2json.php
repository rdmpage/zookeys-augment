<?php

// Extract citations from XML and output in BibJSON

require_once (dirname(__FILE__) . '/lib/extract.php');
require_once (dirname(__FILE__) . '/lib/lib.php');

$filename = '';
if ($argc < 2)
{
	echo "Usage: load xml2json.php <names file>\n";
	exit(1);
}
else
{
	$filename = $argv[1];
}

$file = @fopen($filename, "r") or die("couldn't open $filename");

$xml = file_get_contents($filename);

if ($xml != '')
{
	$collection = mixed_citations($xml);
	
	echo json_format(json_encode($collection));
}



?>