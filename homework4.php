<?php
include 'SpellCorrector.php';
header('Content-Type:text/html;charset=utf-8');
$limit=10;
$query=isset($_REQUEST['q'])?$_REQUEST['q']:false;
$results=false;
$newQuer="";
$QC=false;
if($query)
{
//The Apache Solr Client library should be on the include path
//which is usually most easily accomplished by placing in the
//same directory as this script (. or current directory is a default
//php include path entry in the php.ini)

require_once('Apache/Solr/Service.php');

//create a new solr service instance - host, port, and corename
//path (all defaults in this example)

$solr=new Apache_Solr_Service('localhost',8983,'/solr/myexample/');

//if magic quotes is enabled then stripslashes will be needed

if (get_magic_quotes_gpc()==1)
{
$query=stripslashes($query);
}


//in production code you'll always want to use a try/catch for any
//possible exceptions emitted by searching (i.e. connection
//problems or a query parsing error)
//SpellCorrector::correct('weathr');

$queryCorr=explode(" ",$query);
$newQuery=array();
for($i=0;$i<(sizeof($queryCorr));$i++)
{
  $newQuery[$i]=SpellCorrector::correct($queryCorr[$i]);
}
for($j=0;$j<(sizeof($queryCorr));$j++)
{
	$newQuer.=$newQuery[$j]." ";
}
if(($query." ")!=$newQuer)
{	$QC=true;
	$query=$newQuer;
}
	
try
{
if(isset($_GET['rank']) && ($_GET['rank']=="PageRank"))
{
$start=0;
$rows=10;
$additionalParameters=array('sort'=>"PageRankData desc");
$results=$solr->search($query,$start,$rows,$additionalParameters);
}
else
{
$results=$solr->search($query,0,$limit);}
}
catch
(Exception $e)
{
//in production you'd probably log or email this error to an admin 
//and then show a special message to the user but for this example
//we're going to show the full exception

die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
}
}
?>

<html>
<head>
<title>PHP Solr Client Example</title>
<link rel="stylesheet" href="css/mystyle.css" />
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/script.js"></script>
</head>
<body>
<form id="myForm" accept-charset="utf-8" method="get">
<div>
<div><label for="q">Search:</label></div>
<div class="dropdown"> 
<input id="q" name="q" type="text" value="<?php echo htmlspecialchars($query,ENT_QUOTES,'utf-8');?>" onkeyup="autocomplet()" /> 
<div class="myDropdown"><ul style="list-style-type:none" id="suggest_list_id"></ul></div>
</div>
<div> <input type="radio" name="rank" value="SOLR" checked<?php if(isset($_GET['rank']) && $_GET['rank'] == 'SOLR')  echo ' checked="checked"';?>/> Solr&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="rank" value="PageRank"<?php if(isset($_GET['rank']) && $_GET['rank'] == 'PageRank')  echo ' checked="checked"';?>/> PageRank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit"/></div></div>
</form>

<?php
//display results
if($results)
{
$total=(int)$results->response->numFound;
$start=min(1,$total);
$end=min($limit,$total);
?>
<div>
<p><?php if($QC) {echo 'Showing Results For: ',$query;}  ?></p>
Results <?php echo $start; ?>-<?php echo $end;?> of <?php echo $total;  ?>:</div>
<ol>
<?php
$urlArray=array();
try
{
$file = fopen("UrlMap.csv","r");
while(!feof($file))
{
$arr=fgetcsv($file);
$urlArray[$arr[1]]=$arr[0];
}
fclose($file);
}
catch (Exception $e) 
{
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
//iterate result documents
foreach($results->response->docs as $doc)
{
?>
<?php
 $id=$doc->id;
 $file_name=str_replace("/home/anamika/shared/","",$id);
 $title=$doc->title;
if(isset($doc->author))
	$author=$doc->author;
else $author="NA";
if(isset($doc->date))
	$date=$doc->date;
else $date="NA";
$stream_size=$doc->stream_size;
if($stream_size<1024)	{ $file_size="B";	}
else if($stream_size<1048576){   $stream_size=$stream_size/1024;    $file_size="KB"; }
else {  $stream_size=$stream_size/1048576;  $file_size="MB"; }
$size=(int)$stream_size;
?>
<li>
<p><a href="<?php echo $urlArray[$file_name];?>">Document</a> <?php echo $title;?> </p>
<p>File size: <?php echo $size,$file_size;?> &nbsp;&nbsp;&nbsp;&nbsp;Author: <?php echo $author;?>&nbsp;&nbsp;&nbsp;&nbsp;Date: <?php echo $date;?></p>
</li>

<?php 
}
?>
</ol>
<?php
}
?>
</body>
</html>
