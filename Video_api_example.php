<?php
//Video Import examples
ini_set( "display_errors", 1 );
echo '<pre>';

require_once 'VideoAPILibrary/AdferoArticlesVideoExtensions/AdferoVideoClient.php';
require_once 'VideoAPILibrary/AdferoArticles/AdferoClient.php';
require_once 'VideoAPILibrary/AdferoPhotos/AdferoPhotoClient.php';

define("PUBLIC_KEY", "B335A8BD");
define("PRIVATE_KEY", "a09f9768-31e5-4eb9-ac77-ed02dc259e71");
//Feed Number is the node index from the feeds.xml retrieval.  This number is usually 0(zero) unless you are receiving multiple video feeds.
define("FEED_NUMBER", 0);
//define("FEED_ID", "");

define("API_DOMAIN", "https://api.brafton.com");

define("API_VIDEO_URL", "https://livevideo.api.brafton.com/v2/");
define("PHOTO_URL", "https://pictures.brafton.com/v2/");

/*
 * Load the 3 main Clients
 * $connection = string/boolean(false) force a conneciton type of curl or fopen
 * */
$connection = "curl";
//Load the API client
$Client = new AdferoClient(API_VIDEO_URL, PUBLIC_KEY, PRIVATE_KEY, $connection);

//Load the Video Client
$VideoClient = new AdferoVideoClient(API_VIDEO_URL, PUBLIC_KEY, PRIVATE_KEY, $connection);

//Load the Photo Client used to retrieve the actual photo files
$PhotoClient = new AdferoPhotoClient(PHOTO_URL, $connection);


//Get the Feed Id
$feeds = $Client->Feeds()->ListFeeds(0,10);
$feedId = $feeds->items[FEED_NUMBER]->id;


//Video OutputClient
$VideoClientOutputs = $VideoClient->videoOutputs();

$ArticlePhotosClient = $Client->ArticlePhotos();

//Set up the Categories Client
$CategoryClient = $Client->Categories();

$ArticlesClient = $Client->Articles();

$Articles = $ArticlesClient->ListForFeed($feedId, 'live', 0, 100);
$ArticleCount = count($Articles->items);

foreach($Articles->items as $article){
    //Get the Video ID
    $brafton_id = $article->id;
    //Retrieve the information for the current Video article using the ArticlesClient
    $currentArticle = $ArticlesClient->Get($brafton_id);
    //Retreive the pre and post Splash images.  Post splash is often the Logo used at the end of the video
    $splash = array(
        'Pre'   => $currentArticle->fields['preSplash'],
        'Post'  => $currentArticle->fields['postSplash']
    );
    
    $video_content = $currentArticle->fields['content'];
    $video_title = $currentArticle->fields['title'];
    $video_excerpt = $currentArticle->fields['extract'];
    $video_date = $currentArticle->fields['date'];
    
    //array of Category id objects containing only the id
    $video_category_ids = $CategoryClient->ListForArticle($brafton_id, 0,100)->items;
    
    //array of Category Names associated with this video
    $video_categories = array();
    foreach($video_category_ids as $vc_id){
        $video_categories[] = $CategoryClient->Get($vc_id->id)->name;   
    }
    
    //Get the Videos. returns array of video objects with the id for the video
    $videoList = $VideoClientOutputs->ListForArticle($brafton_id, 0,10)->items;
    
    //Array of information for each video in the feed
    $videos = array();
    foreach($videoList as $v_id){
        $output = $VideoClientOutputs->Get($v_id->id);
        $vid = new stdClass();
        $vid->id = $v_id->id;
        $vid->type = $output->type;
        $vid->path = $output->path;
        $vid->height = $output->height;
        $videos[] = $vid;
    }
    //Get the id of the image from the video article
    $ap_id = $ArticlePhotosClient->ListForArticle($brafton_id, 0,100)->items[0]->id;
    
    $photoObject = $ArticlePhotosClient->Get($ap_id);
    //Get the master source id for the photo
    $p_id = $photoObject->sourcePhotoId;
    //set the width of the image you want to download
    $size = 500;
    //Get the url of the image from braftons master repository for use in downloading
    $photo['url'] = $PhotoClient->Photos()->GetScaleLocationUrl($p_id, "x", $size)->locationUri;
    $photo['caption'] = $photoObject->fields['caption'];
    $photo['alt'] = $photoObject->fields['altText'];
    $array = compact("brafton_id", "splash", "video_title", "video_content", "video_excerpt", "video_date", "video_categories", "videos", "photo");
    var_dump($array);
    exit();
    
}
?>