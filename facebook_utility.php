<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class FacebookUtility {
  private $user_id;
  private $user;
  private $user_profile;
  private $albums;
  private $friends;
  private $facebook;
  private $login_url;
  private $logout_url;
  private $check_login;
  
  public function __construct() {
    define('FB_APP_ID', '451633734906371');
    define('FB_APP_SECRET', '7db75384d226678c04f63dd49cd33882');
    define('SITE_BASE_URL', 'http://localhost/facebookapp/index.php');
    define('SITE_REDIRECT_LOGOUT', 'http://localhost/facebookapp/index.php?logout=true');
    define('SITE_CANCEL_URL', 'http://localhost/facebookapp/index.php/');
    define('SITE_NEXT_URL', 'http://localhost/facebookapp/index.php/');
    define("THEME",'twitter-bootstrap');
    define("LIMIT_PAGE",20);
    session_start();
    require 'facebook-php-sdk/facebook.php';
    $this->facebook = new Facebook(array(
            'appId' => FB_APP_ID,
            'secret' => FB_APP_SECRET,
        ));
    $user = $this->get_user();
    if( $user ) {
      $this->set_user_profile();
      $this->check_login = true;
      $this->set_logout_url ();
      
    }     
    else {
      $this->check_login = false;
      $this->set_login_url();
    }
      

  }
  
  public function set_user_id( $user_id ) {
    $this->user_id = $user_id;
  }
  public function get_user_id() {
    return $this->user_id;
  }
  
  public function get_user() {
    $this->user = $this->facebook->getUser();
    return $this->user;
  }
  public function set_user_profile() {
    $this->user_profile = $this->facebook->api('/me');
    return $this->user_profile;
  }
  public function get_user_profile() {
    return $this->user_profile;
  }
  public function set_login_url() {
    $this->login_url = $this->facebook->getLoginUrl(array(
        'scope' => 'email,user_birthday,status_update,publish_stream,user_photos',
        'next' => SITE_NEXT_URL,
        'cancel_url' => SITE_CANCEL_URL,
        'display'=> 'popup'
       ));
  }
  public function get_login_url() {
    return $this->login_url;
  }
  public function set_logout_url() {
    $this->logout_url = $this->facebook->getLogoutUrl(array("next"=>SITE_REDIRECT_LOGOUT));
  }
  public function get_logout_url() {
    return $this->logout_url;
  }
  public function get_albums() {
    $this->albums = $this->facebook->api('/me/albums');
    return $this->albums;
  }
  
  public function get_album_by_id( $album_id ) {
    $results = $this->facebook->api('/'. $album_id .'/photos');
    return $results;
    
  }
  public function get_friends($params) {
    $this->friends = $this->facebook->api('/me/friends?' . $params);
    if(!empty($this->friends))
      return $this->friends;
    else {
      return false;
    }
  }
  public function get_friend_by_id( $id, $param ) {
//    $results = $this->facebook->api('/?fields=about,bio,age_range,first_name,gender,address,email,location,link,languages,username,last_name,timezone,updated_time&ids='.$id);
    $results = $this->facebook->api('/?fields=' . $param . '&ids='.$id);
    return $results;
  }
  public function logout() {
    if(isset($_REQUEST['logout']) && $_REQUEST['logout'] == true) {
      $this->facebook->destroySession();
    }

  }
  
  public function get_check_login() {
    return $this->check_login;
  }
  public function get_liked_of_page( $url ) {
    try {
        $countLike = $this->facebook->api(array(
            'method' => 'fql.query',
            'query' => '
                SELECT share_count, like_count, comment_count, total_count
                FROM link_stat
                WHERE url="'.$url.'"
            ',
            )
        );
        return $countLike;
    } catch(FacebookApiException $e) {
        return $e->getMessage();
    }
  }
  
  public function post_comment( $param = array() ) {
    try{
//    var_dump($_POST);exit;

        if(!empty($param)){
            $message = $param['message'];
            $description = $param['description'];
            $name = $param['name'];
            $attachment = array(
                'access_token' => $this->facebook->getAccessToken(),
                'name' => $name,
                'description' => $description,
                'message' => $message,
//                'picture' => '',
                'link' => SITE_BASE_URL
            );
            $id = $this->user_id;
            $result = $this->facebook->api('/' . $id . '/feed', 'POST', $attachment);
            if($result){
                echo json_encode(array('status'=>1));

            }else
                echo json_encode(array('status'=>0));
            exit;
        }

}  catch (FacebookApiException $e){

    var_dump($e);

}
  }
  
  public function get_pageinfo_by_id( $id ) {
    $page_info = $this->facebook->api($id);
    return $page_info;
  }
  
  
}
?>
