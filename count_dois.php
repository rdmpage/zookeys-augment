<?php

// Count number of DOIs in an XML file

require_once(dirname(__FILE__) . '/lib/extract.php');

$filename = '';
if ($argc < 2)
{
	echo "Usage: load count_dois.php <names file>\n";
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
	$count = 0;
	
	// Get the mixed citations from the XML
	$collection = mixed_citations($xml);
		
	// Augment by adding DOIs
	$num_records = count($collection->records);
	for ($i = 0; $i < $num_records; $i++)
	{
		$doi = reference_one_identifier($collection->records[$i], 'doi');
			
		if ($doi != '')
		{
			$count++;
		}
			
	}
	
	echo "References: " . count($collection->records) . " of which " . $count . " have DOIs\n";
	
}
else
{
	echo "Problem reading XML\n";
}
	


?>