<?php
/**

 * 
 * @package news
 * @author William Newman
 * @version 2.12 2015/06/04
 * @link http://newmanix.com/ 
 * @license http://www.apache.org/licenses/LICENSE-2.0

 */
 
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials
$config->metaRobots = 'no index, no follow';

# check variable of item passed in - if invalid data, forcibly redirect back to index.php page
if(isset($_GET['id']) && (int)$_GET['id'] > 0){#proper data must be on querystring
	 $myID = (int)$_GET['id']; #Convert to integer, will equate to zero if fails
}else{
	myRedirect(VIRTUAL_PATH . "news/index.php");
}


$myItem = new Feed($myID);

if($myItem->IsValid)
{#only load data if record found
	$config->titleTag = $myItem->ItemTitle . " - News curated with PHP & cupcakes!";

}
//get rss feed xml 
    $feedXML = $myItem->ItemLink; //get rss XML url string from Feed object
    $feedXML = file_get_contents($feedXML);
    $xml = simplexml_load_string($feedXML);//get RSS XML file from source and convert to object
    print'<h1>' . $xml->channel->title . '</h1>';

$namespaces = $xml->getNamespaces(true); //get namespaces from xml (needed to parse media:content tags in certain rss feeds)


#END CONFIG AREA ---------------------------------------------------------- 

get_header(); #defaults to theme header or header_inc.php
?>
<h3><?=$myItem->ItemTitle;?></h3>
<p><?=$myItem->ItemDescription;?></p>
<?php

 

//show data
if($myItem->IsValid) {
    
    foreach($xml->channel->item as $story)
    {
        echo '<a href="' .$story->link . '">' . $story->title . '</a><br>';
        echo '<p>' . $story->description . '</p><br><br>';
    }
    } else {
    echo '
    <div>news not found</div>
    ';
}
get_footer(); #defaults to theme footer or footer_inc.php



class Feed
{
    public $ItemID = 0;
    public $ItemLink = '';
    public $ItemTitle = '';
    public $ItemDescription = '';
    public $Category = '';
    public $Description = '';
    public $IsValid = 'false';
        
    public function __construct($id)
    {
        $this->ItemID = (int)$id;
        
        $sql = '
        select 
            sp19_newsfeeds.ItemLink, 
            sp19_newsfeeds.ItemTitle, 
            sp19_newsfeeds.ItemDescription, 
            sp19_newscategories.Category,
            sp19_newscategories.Description
        from sp19_newsfeeds 
        join sp19_newscategories on sp19_newsfeeds.CategoryID = sp19_newscategories.CategoryID
        where ItemID = ' . $this->ItemID;
        
        # connection comes first in mysqli (improved) function
        $result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));
        
        if(mysqli_num_rows($result) > 0)
        {#records exist - process
            $this->IsValid = true;
            while ($row = mysqli_fetch_assoc($result))
            {
                $this->ItemLink = dbOut($row['ItemLink']);
                $this->ItemTitle = dbOut($row['ItemTitle']);
                $this->ItemDescription = dbOut($row['ItemDescription']);
                $this->Category = dbOut($row['Category']);
                $this->Description = dbOut($row['Description']);
            }
        }
    }//end of Feed Constructor
    
}//end of Feed Class
