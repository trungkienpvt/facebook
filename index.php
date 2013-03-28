<?php
define('FB_APP_ID', '451633734906371');
define('FB_APP_SECRET', '7db75384d226678c04f63dd49cd33882');
define('SITE_BASE_URL', 'http://localhost/facebook/index.php/');
define('SITE_CANCEL_URL', 'http://localhost/facebook/index.php/');
define('SITE_NEXT_URL', 'http://localhost/facebook/index.php/');
define("THEME",'twitter-bootstrap');
define("LIMIT_PAGE",20);

session_start();
require 'facebook-php-sdk/facebook.php';
// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
            'appId' => FB_APP_ID,
            'secret' => FB_APP_SECRET,
        ));
// Get User ID
$user = $facebook->getUser();
if ($user) {
    $logoutUrl = $facebook->getLogoutUrl(array("next"=>SITE_BASE_URL));
} else {
    $loginUrl = $facebook->getLoginUrl(array(
        'scope' => 'email,user_birthday,status_update,publish_stream,user_photos',
        'next' => SITE_NEXT_URL,
        'cancel_url' => SITE_CANCEL_URL,
        'display'=> 'popup'
       ));
}

if ($user) {
    try {
        // Proceed knowing you have a logged in user who's authenticated.
        $user_profile = $facebook->api('/me');
        $requestFriend = $facebook->api('/me/friends');
        $friends= $requestFriend['data'];
        $countFriend = count($friends);
        if(isset($_REQUEST['page']))
            $page = $_REQUEST['page'];
        else $page = 1;
        $limit = LIMIT_PAGE;
        $totalPage = ceil($countFriend/$limit);
        $friendsResult = array_slice($friends, ($page-1)*$limit, $limit);
//        $pageId = 'BaoBongDa';
//	$pageinfo = $facebook->api($pageId);
//	$pageLike = $pageinfo['likes'];
//        $userProfile = $this->facebook->api('/me');
//                print("<pre>");
//    print_r($user_profile);exit;
//        var_dump($user_profile);


//        print("<pre>");
//    print_r($friends);exit;
//        var_dump($user_profile);
    } catch (FacebookApiException $e) {

                    $params = array(
                        'scope' => 'email,user_birthday,status_update,publish_stream,user_photos',
                    );
                    $loginURL = $facebook->getLoginUrl($params);
                    die('<script>window.top.location.href="' . $loginURL . '";</script>');
    }
}
try{
//    var_dump($_POST);exit;

        if(isset($_POST['message'])){
            $message = $_POST['message'];
            $description = $_POST['description'];
            $name = $_POST['name'];
            $attachment = array(
                'access_token' => $facebook->getAccessToken(),
                'name' => $name,
                'description' => $description,
                'message' => $message,
//                'picture' => '',
                'link' => SITE_BASE_URL
            );
            $result = $facebook->api('/' . $user_profile['id'] . '/feed', 'POST', $attachment);
            if($result){
                echo json_encode(array('status'=>1));

            }else
                echo json_encode(array('status'=>0));
            exit;
        }

}  catch (FacebookApiException $e){

    var_dump($e);

}
$urlPageWantToCountLike = 'https://www.facebook.com/onlineshoptk';
try {
        $countLike = $facebook->api(array(
            'method' => 'fql.query',
            'query' => '
                SELECT share_count, like_count, comment_count, total_count
                FROM link_stat
                WHERE url="'.$urlPageWantToCountLike.'"
            ',
            )
        );
//        var_dump('<pre>', $countLike);
    } catch(FacebookApiException $e) {
        die('<script>window.top.location.href="' . $loginUrl . '";</script>');
    }

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Facebook Application</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="../<?php print THEME?>/assets/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>
    <link href="../<?php print THEME?>/assets/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="../<?php print THEME?>/assets/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../<?php print THEME?>/assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../<?php print THEME?>/assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../<?php print THEME?>/assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="../<?php print THEME?>/assets/ico/apple-touch-icon-57-precomposed.png">
  </head>
  <body style="">
    <div class="">
    <div class="container">
      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
          <h1>Facebook Application</h1>
          <?php
          if($user && $user_profile):
          ?>
        <h2>Profile information</h2>
        <table class="table">

            <tr><td>Name</td><td><?php print($user_profile['name'])?></td></tr>
            <tr><td>First Name</td><td><?php print($user_profile['first_name'])?></td></tr>
            <tr><td>Last Name</td><td><?php print($user_profile['last_name'])?></td></tr>
            <tr><td>User Name</td><td><?php print($user_profile['username'])?></td></tr>
            <tr><td>Birthday</td><td><?php print($user_profile['birthday'])?></td></tr>
            <tr><td>Work</td><td><?php print($user_profile['work'][0]['employer']['name'])?></td></tr>
        </table>
        <button class="btn btn-large btn-primary" type="button" onclick="location.href='<?php print $logoutUrl?>';">Logout</button>
        <h2>Friend List</h2>
        <?php $data = $friendsResult?>
        <table class="table">
            <thead>
                <tr>
                    <th>avatar</th><th>Name</th>
                    </tr>
            </thead>
            <?php foreach($data as $row):?>
        <tr><td><img src="https://graph.facebook.com/<?php echo $row['id'] ?>/picture"/></td><td><?php print ($row['name'])?></td>

            </tr>
        <?php endforeach;?>
        </table>
            <div class="pagination">
    <ul>
        <?php
                if($page > 1){
                    $prev = ($page - 1);
                    echo "<a href='" . SITE_BASE_URL . "?page=$prev'>← Previous </a> ";
                }

                for($i = 1; $i <= $totalPage; $i++){
                    if(($page) == $i){
                        echo "<li>$i</li>";
                    } else {
                        echo "<li><a href='" . SITE_BASE_URL . "?page=$i'>$i</a></li> ";
                }
                }
        // Build Next Link
                if($page < $totalPage){
                    $next = ($page + 1);
                    echo "<li><a href='" . SITE_BASE_URL . "?page=$next'>Next →</a></li>";
                }

        ?>

    </ul>
    </div>
        <a href="<?php echo $friends['paging']['next']?>">Next</a>
        <?php else:?>
        <h3>Please <a href="<?php print $loginUrl?>">Login</a> to access your information in facebook</h3>
        <?php endif?>
        <form action="<?php print SITE_BASE_URL ?>" name="formsend" id='formsend' method="post">
            <fieldset>
            <legend>Post Example</legend>
            <label>Message</label>
            <textarea type="text" id="message" name="message" placeholder="Type something…"></textarea>
            <label>Name:</label>
            <input type="text" name="name" id="name"/>
            <label>Description:</label>
            <input type="text" name="description" id="description"/>
            <button type="button" id="sendmessage" name="sendmessage" class="btn">Send Message</button>
            </fieldset>
        </form>
        <h2>Like number of page :<span class="badge badge-important"><?php print $countLike[0]['like_count'] ?></span></h2>
      </div>
      <!-- Example row of columns -->
      <hr>
      <footer>
        <p>&copy; Company 2012</p>
      </footer>
    </div> <!-- /container -->
    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="<?php print THEME?>/assets/js/jquery.js"></script>
    <script src="<?php print THEME?>/assets/js/bootstrap-transition.js"></script>
    <script src="<?php print THEME?>/assets/js/bootstrap-alert.js"></script>
    <script src="<?php print THEME?>/assets/js/bootstrap-modal.js"></script>
    <script src="<?php print THEME?>/assets/js/bootstrap-dropdown.js"></script>
    <script src="<?php print THEME?>/assets/js/bootstrap-scrollspy.js"></script>
    <script src="<?php print THEME?>/assets/js/bootstrap-tab.js"></script>
    <script src="<?php print THEME?>/assets/js/bootstrap-tooltip.js"></script>
    <script src="<?php print THEME?>/assets/js/bootstrap-popover.js"></script>
    <script src="<?php print THEME?>/assets/js/bootstrap-button.js"></script>
    <script src="<?php print THEME?>/assets/js/bootstrap-collapse.js"></script>
    <script src="<?php print THEME?>/assets/js/bootstrap-carousel.js"></script>
    <script src="<?php print THEME?>/assets/js/bootstrap-typeahead.js"></script>
    <script type="text/javascript" language="javascript">
    $(document).ready(function(){
        $('#sendmessage').click(function(){
            $.post('<?php print SITE_BASE_URL ?>',$("#formsend").serialize(),function(data){
                if(data.status ==1)
                        alert('Post message successfull');
                    else
                        alert("Post message fail");
            },'json');
        });
        });
    </script>
  </body>
</html>
