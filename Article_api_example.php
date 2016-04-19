<?php
//Article Import examples
require_once 'ArticleAPILibrary/ApiHandler.php';
ini_set( "display_errors", 1 );
echo '<pre>';
/*
 * Define your API_KEY (GUID provided by your Account manager);
 * Define your API_DOMAIN eg. https://api.brafton.com
 * 
 * */
define("API_KEY", '304908f3-50d2-4aae-95a6-eb013feb1fc6');
define("API_DOMAIN", 'https://api.brafton.com/');

/*
 * Estabilish an API Connection
 * API_KEY = string (GUID)
 * API_DOMAIN = string (Base Url)
 * (optional) $type = string/boolean(false);
 * */
$type = false; //false or "curl" or "fopen". If false will determine available connection options. 
$connection = new ApiHandler(API_KEY, API_DOMAIN, $type );


/*
 * Retrieve a list of articles from the XML feed
 * {API_DOMAIN/API_KEY/news}
 * 
 * */

$categories = $connection->getCategoryDefinitions();
//Retrieve a complete list of articles (Last 30 days or 50 articles)
$articles = $connection->getNewsHTML();

//Retrieve a specific article denoted by ID
//$articles = $connection->getNewsItem(40116259);

/*
 * Loop through the list of articles
 * 
 * */
foreach($articles as $article){
    //var_dump("First article",$article);
    //use this id to uniquely identify your articles.
    $id = $article->getId();
    
    //Article Attributes
    $title = $article->getHeadline();
    $body = $article->getText();
    $publish_date = $article->getPublishDate();
    $created_date = $article->getCreatedDate();
    $modified_date = $article->getLastModifiedDate();
    
    // Optional article Attributes.  Items listed here are optional only and may not exist in your feed
    //See NewsCategory.php for information about the Category object
    $categories = $article->getCategories(); //returns array of category objects. 
    $keywords = $article->getKeywords(); //returns string
    //See Photo.php for information about the Photo object and PhotoInstance.php for information about each instance object.
    $photos = $article->getPhotos(); //returns array of photo instance objects. 
    $photoId = $photos[0]->getId();
    $photoAltText = $photos[0]->getAlt();
    $photoOrientation = $photos[0]->getOrientation();
    $photoCaption = $photos[0]->getCaption();

    $extract = $article->getExtract(); //return string
    $byline = $article->getByLine(); //return string
    $tweet = $article->getTweetText(); //return string
    $htmlTitle = $article->getHtmlTitle(); //return string
    $htmlMetaDescription = $article->getHtmlMetaDescription(); //return string
    $htmlMetaKeywords = $article->getHtmlMetaKeywords(); //return string
    $tags = $article->getTags(); //return string
    
    $array = compact('id', 'title', 'body', 'publish_date', 'created_date', 'modified_date', 'categories', 'keywords', 'photos', 'extract', 'byline', 'tweet', 'htmlTitle', 'htmlMetaDescription', 'htmlMetaKeywords', 'tags');
    //var_dump($array);
    //establish connection with your platforms Database and store the article
}

?>