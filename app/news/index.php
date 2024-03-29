<?php
//app/news/index.php
/**
 * index.php along with news_view.php allows us to view news items.
 *
 * @package News
 * @author Carolina Ferraz <carol@gmail.com>
 * @version 1 2019/05/09
 * @link http://cferraz.000webhostapp.com
 * @license https://www.apache.org/licenses/LICENSE-2.0
 * @see survey_pager.php
 * @see Pager.php 
 * @todo none
 */

# '../' works for a sub-folder.  use './' for the root  
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials 
 
# SQL statement

$sql = "select c.CategoryID, c.Category, c.Description, f.ItemID, f.ItemTitle, f.ItemLink, f.ItemDescription from sp19_newscategories c, sp19_newsfeeds f where c.CategoryID=f.CategoryID ";

#Fills <ItemTitle> tag. If left empty will default to $PageItemTitle in config_inc.php  
$config->ItemTitleTag = 'News items curated made with cupcakes & PHP in Seattle';

#Fills <meta> tags.  Currently we're adding to the existing meta tags in config_inc.php
$config->metaDescription = 'Seattle Central\'s ITC250 Class ' . $config->metaDescription;
$config->metaKeywords = 'News,PHP,Fun,Cupcakes,Strawberry,Regular Expressions,'. $config->metaKeywords;

//adds font awesome icons for arrows on pager
$config->loadhead .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';

/*
$config->metaDescription = 'Web Database ITC250 class website.'; #Fills <meta> tags.
$config->metaKeywords = 'SCCC,Seattle Central,ITC250,database,mysql,php';
$config->metaRobots = 'no index, no follow';
$config->loadhead = ''; #load page specific JS
$config->banner = ''; #goes inside header
$config->copyright = ''; #goes inside footer
$config->sidebar1 = ''; #goes inside left side of page
$config->sidebar2 = ''; #goes inside right side of page
$config->nav1["page.php"] = "New Page!"; #add a new page to end of nav1 (viewable this page only)!!
$config->nav1 = array("page.php"=>"New Page!") + $config->nav1; #add a new page to beginning of nav1 (viewable this page only)!!
*/

# END CONFIG AREA ---------------------------------------------------------- 

get_header(); #defaults to theme header or header_inc.php
?>
<h3 align="center">News List</h3>
<?php

#images in this case are from font awesome
$prev = '<i class="fa fa-chevron-circle-left"></i>';
$next = '<i class="fa fa-chevron-circle-right"></i>';

# Create instance of new 'pager' class
$myPager = new Pager(10,'',$prev,$next,'');
$sql = $myPager->loadSQL($sql);  #load SQL, add offset

# connection comes first in mysqli (improved) function
$result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));




@mysqli_free_result($result);
get_footer(); #defaults to theme footer or footer_inc.php 