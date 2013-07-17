<?php

// Extract references from NLM XML

require_once(dirname(__FILE__) . '/reference.php');

function mixed_citations ($xml)
{
	$collection = new stdclass;
	$collection->records = array();
	
	$dom= new DOMDocument;
	$dom->loadXML($xml);
	$xpath = new DOMXPath($dom);
	
	$refCollection = $xpath->query ('//ref');
	foreach($refCollection as $ref)
	{
		$citation = new stdclass;
	
		if ($ref->hasAttributes()) 
		{ 
			$attributes = array();
			$attrs = $ref->attributes; 
			
			foreach ($attrs as $i => $attr)
			{
				$attributes[$attr->name] = $attr->value; 
			}
		}
		if (isset($attributes['id']))
		{
			$citation->id = $attributes['id'];
		}
		
		$citation->type = 'generic';		
	
		$nodeCollection = $xpath->query ('mixed-citation', $ref);
		foreach($nodeCollection as $node)
		{
			$nc = $xpath->query ('article-title', $node);
			foreach($nc as $n)
			{
				$citation->type = 'article';
				$citation->title = $n->firstChild->nodeValue;
			}	

			$nc = $xpath->query ('person-group', $node);
			foreach($nc as $n)
			{
				$citation->author = array();
				$nameCollection = $xpath->query ('name', $n);
				foreach($nameCollection as $name)
				{
					$author = new stdclass;

					$partCollection = $xpath->query ('given-names', $name);
					foreach($partCollection as $part)
					{
						$author->firstname = $part->firstChild->nodeValue;
						$author->name = $author->firstname;
					}	

					$partCollection = $xpath->query ('surname', $name);
					foreach($partCollection as $part)
					{
						$author->lastname = $part->firstChild->nodeValue;
						$author->name = trim(' ' . $author->lastname);
					}
					
					$citation->author[] = $author;

				}
			}							
	
			$nc = $xpath->query ('source', $node);
			foreach($nc as $n)
			{
				switch ($citation->type)
				{
					case 'article':
						$citation->journal = new stdclass;
						$citation->journal->name = $n->firstChild->nodeValue;					
						break;
						
					default:
						break;
				}			
			}
			
			$nc = $xpath->query ('volume', $node);
			foreach($nc as $n)
			{
				switch ($citation->type)
				{
					case 'article':
						$citation->journal->volume = $n->firstChild->nodeValue;					
						break;
						
					default:
						break;
				}			
			}
	
			$nc = $xpath->query ('issue', $node);
			foreach($nc as $n)
			{
				switch ($citation->type)
				{
					case 'article':
						$citation->journal->issue = $n->firstChild->nodeValue;					
						break;
						
					default:
						break;
				}			
			}
	
			$nc = $xpath->query ('fpage', $node);
			foreach($nc as $n)
			{
				switch ($citation->type)
				{
					case 'article':
						$citation->journal->pages = $n->firstChild->nodeValue;					
						break;
						
					default:
						$citation->pages = $n->firstChild->nodeValue;
						break;
				}			
			}
	
			$nc = $xpath->query ('lpage', $node);
			foreach($nc as $n)
			{
				switch ($citation->type)
				{
					case 'article':
						$citation->journal->pages .= '--' . $n->firstChild->nodeValue;					
						break;
						
					default:
						$citation->pages .= '--' . $n->firstChild->nodeValue;
						break;
				}			
			}		

			$nc = $xpath->query ('year', $node);
			foreach($nc as $n)
			{
				$citation->year = $n->firstChild->nodeValue;					
			}
	
			$nc = $xpath->query ('ext-link', $node);
			foreach($nc as $n)
			{
				if ($n->hasAttributes()) 
				{ 
					$attributes = array();
					$attrs = $n->attributes; 
					
					foreach ($attrs as $i => $attr)
					{
						$attributes[$attr->name] = $attr->value; 
					}
				}
				
				if (isset($attributes['ext-link-type']))
				{
					switch ($attributes['ext-link-type'])
					{
						case 'uri':
							if (preg_match('/dx.doi.org\/(?<doi>.*)/', $attributes['href'], $m))
							{
								$identifier = new stdclass;
								$identifier->type 	= 'doi';
								$identifier->id		= $m['doi'];
								$citation->identifier[] = $identifier;
							}
							break;
							
						default:
							break;
					}
				}
			}
			
			$citation->citation = reference_to_citation_string($citation);
			
			$collection->records[] = $citation;
		}		
	}

	return $collection;	
}

?>