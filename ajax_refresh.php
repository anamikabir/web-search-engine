<?php
if(isset($_POST['keyword']))
{
	$query = $_POST['keyword'];
	try
	{	$newQ=trim($query);
		$words = explode(" ", $newQ);
		$siz=(sizeof($words))-1;
		$toFind=$words[$siz];
		$url='http://localhost:8983/solr/myexample/suggest?q='.$toFind.'&wt=json&indent=true';
		$res=file_get_contents($url);
		$rq="";
		for($i=0;$i<$siz;$i++)
		{
			$rq.=$words[$i]." ";
		}
		//echo $siz;
		$json = json_decode($res,true);
		//var_dump($words);
		//echo $json["suggest"]["suggest"][$query]["suggestions"][0]["term"];
		$suggestions = $json["suggest"]["suggest"]["$toFind"]["suggestions"];
		foreach($suggestions as $currentSuggestion)
		{
			$suggTerm=$currentSuggestion["term"];
			$sugg=$rq.$suggTerm;
			echo '<li onclick="set_item(\''.str_replace("'", "\'", $sugg).'\')">'.$sugg.'</li>';
		}
		
	}
	catch(Exception $e){die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");}
}
?>
