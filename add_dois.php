<?php

require_once(dirname(__FILE__) . '/lib/crossref.php');
require_once(dirname(__FILE__) . '/lib/extract.php');

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
	// Get the mixed citations from the XML
	$collection = mixed_citations($xml);
	
	// Load XML
	$dom= new DOMDocument;
	$dom->loadXML($xml);
	$xpath = new DOMXPath($dom);
	
	// Augment by adding DOIs
	$num_records = count($collection->records);
	for ($i = 0; $i < $num_records; $i++)
	{
		if ($collection->records[$i]->type == 'article')
		{
			$doi = reference_one_identifier($collection->records[$i], 'doi');
			
			if ($doi == '')
			{
				// No DOI so let's go look for one			
				if (crossref_lookup($collection->records[$i], true))
				{					
					// add this to source XML
					// $node is the mixed-citation node
					
					$doi = reference_one_identifier($collection->records[$i], 'doi');
					
					// If we have a DOI add it to the XML
					if ($doi != '')
					{					
						// Get this node in the XML
						$nodeCollection = $xpath->query ('//ref[@id="' . $collection->records[$i]->id . '"]/mixed-citation');
						foreach($nodeCollection as $node)
						{
							$ext_link = $node->appendChild($dom->createElement('ext-link'));
							$ext_link->setAttribute('ext-link-type', 'uri');
							$ext_link->setAttribute('xlink:href', 'http://dx.doi.org/' . $doi);		
							$ext_link->setAttribute('xlink:type', 'simple');
		
							$ext_link->appendChild($dom->createTextNode($doi));
						}
					}
				}
			}
		}			
	}
	
	// output updated XML
	echo $dom->saveXML();	
	
}	
	


?>