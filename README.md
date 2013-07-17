zookeys-augment
===============

Experiments in augmenting ZooKeys XML by adding additional citation identifiers.

#### add_dois.php

This script takes XML for the journal ZooKeys (see https://github.com/pensoft/ZooKeys-xml) and uses services from [CrossRef](http://www.crossref.org) to add DOIs to citations that lack them. Any DOIs are added back into the XML.

	php add_dois.php <zookeys XML file>

will dump the augmented XML to standard output. You can redirect this, e.g.:

	php add_dois.php <zookeys XML file> > <new XML file>

#### xml2json.php

Output all citations (marked by the <mixed-citation> tag) BibJSON format.


	php xml2json.php <zookeys XML file>

#### count_dois.php

Count the number of citations that have DOIs.


	php count_dois.php <zookeys XML file>

