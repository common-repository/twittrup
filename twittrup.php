<?php
/*
Plugin Name: Twittrup
Plugin URI: http://plugins.wirtschaftsinformatiker.cc/wordpress/twittrup/
Description: Updates Twitter when you create a new blog post utilizing any shortener
Version: 1.1
Author: Marco Bischoff
Author URI: http://www.wirtschaftsinformatiker.cc
*/

if(function_exists('load_plugin_textdomain'))
	load_plugin_textdomain('twittrup', false, dirname(plugin_basename(__FILE__)) . '/languages');


function twittrup_doPost($sTweet, $sTwitterURI) {

	$sHost = 'twitter.com';
	$sAgent = 'twittrup (Wordpress Plugin)';
	$iPort = 80;
	
	$iAccounts = get_option('twittrup_accountnr');
	
	for($iCount = 1; $iCount < ($iAccounts+1); ++$iCount) {
		$oFp = fsockopen($sHost, $iPort, $iErr_num, $sErr_msg, 10);
		$sUserOption = "twittrup-twitterlogin_username_".$iCount;
		
		$sUser = get_option($sUserOption);
		$sPassOption = "twittrup-twitterlogin_password_".$iCount;
		$sPass = get_option($sPassOption);
		$sPassB64 = base64_decode($sPass);
		$sPassR = ereg_replace("twittruppass:", "", $sPassB64);
		
		$sLoginDetails = base64_encode($sUser.":".$sPassR);
	
		if($sLoginDetails != '') {
			if (!$oFp) {
				echo "$sErr_msg ($iErr_num)<br>\n";
			} else {
				fputs($oFp, "POST $sTwitterURI HTTP/1.1\r\n");
				fputs($oFp, "Authorization: Basic ".$sLoginDetails."\r\n");
				fputs($oFp, "User-Agent: ".$sAgent."\n"); 
				fputs($oFp, "Host: $sHost\n");
				fputs($oFp, "Content-type: application/x-www-form-urlencoded\n");
				fputs($oFp, "Content-length: ".strlen($sTweet)."\n");
				fputs($oFp, "Connection: close\n\n");
				fputs($oFp, $sTweet);
				for ($i = 1; $i < 10; $i++){$sResponse = fgets($oFp, 256);}
				fclose($oFp);
			}
			
		} else {
			//user has not entered details.. Do nothing? Don't wanna mess up the post saving..
		}
	}

	return '';
}

function twittrup_tweet_save($post_ID) {
	global $post;
	
	$sTwitterURI = '/statuses/update.xml';
	
	$oPostTitle = get_post($post_ID);
	$sPostTitle = $oPostTitle->post_title;
	$sPostLink = get_permalink($post_ID);
	$sSentence = '';
	$sShortUrl = '';	
	
	//New Post published
	if(get_option('twittrup-post-created') == '1') {
		if($_POST['post_status'] == 'publish' && $_POST['originalaction'] == 'post') {
			$sSentence = get_option('twittrup-post-created-text');
			
			if(get_option('twittrup-post-publish-showlink') == '1'){
				$sShortUrl = shortenurl($sPostLink);
				$sPostTitle = $sPostTitle . ' ' . $sShortUrl . '';
			}
			$sSentence = str_replace ( '#title#', $sPostTitle, $sSentence);
		}
	}
	
	//Edit Post
	if(get_option('twittrup-post-edit') == '1') {
		if($_POST['post_status'] == 'publish' && $_POST['originalaction'] == 'editpost') {
			$sSentence = get_option('twittrup-post-edit-text');
			
			if(get_option('twittrup-post-edit-showlink') == '1'){
				$sShortUrl = shortenurl($sPostLink);
				$sPostTitle = $sPostTitle . ' ' . $sShortUrl . '';
			}
			$sSentence = str_replace ( '#title#', $sPostTitle, $sSentence);
		}
	}
	
	//Create Draft
	if(get_option('twittrup-draft-created') == '1') {
		if($_POST['post_status'] == 'draft' && $_POST['originalaction'] == 'post') {
			$sSentence = get_option('twittrup-draft-created-text');
			
			if(get_option('twittrup-draft-created-showlink') == '1'){
				$sShortUrl = shortenurl($sPostLink);
				$sPostTitle = $sPostTitle . ' ' . $sShortUrl . '';
			}
			$sSentence = str_replace ( '#title#', $sPostTitle, $sSentence);
		}
	}
	
	//Edit Draft
	if(get_option('twittrup-draft-edit') == '1') {
		if($_POST['post_status'] == 'draft' && $_POST['orignalaction'] == 'editpost') {
			$sSentence = get_option('twittrup-draft-edit-text');
			
			if(get_option('twittrup-draft-edit-showlink') == '1'){
				$sShortUrl = shortenurl($sPostLink);
				$sPostTitle = $sPostTitle . ' ' . $sShortUrl . '';
			}
			$sSentence = str_replace ( '#title#', $sPostTitle, $sSentence);
		}
	}
	
	//Draft to publish
	if(get_option('twittrup-draft-publish') == '1') {
		if($_POST['original_post_status'] == 'draft' && $_POST['post_status'] == 'publish' && $_POST['originalaction'] == 'editpost') {
			$sSentence = get_option('twittrup-draft-publish-text');
			
			if(get_option('twittrup-draft-publish-showlink') == '1'){
				$sShortUrl = shortenurl($sPostLink);
				$sPostTitle = $sPostTitle . ' ' . $sShortUrl . '';
			}
			$sSentence = str_replace ( '#title#', $sPostTitle, $sSentence);
		
		}
	}
	
	if($sSentence != ''){
		
		twittrup_doPost('status='.urlencode($sSentence), $sTwitterURI);
	}
	
	return $post_ID;
	
}

// ADMIN PANEL - under Manage menu

function twittrup_addTwitterAdminPages() {
    if (function_exists('add_management_page')) {
		 add_management_page('TwittrUp', 'TwittrUp', 8, __FILE__, 'twittrup_Twitter_manage_page');
    }
 }

function twittrup_Twitter_manage_page() {
    include(dirname(__FILE__).'/twittrup_updater_manage.php');
}

function shortenurl($sUrl) {
	$sShortUrl = "";
	
	switch(get_option('twittrup-service')) {
		case 1:		$sShortUrl = file_get_contents("http://tinyurl.com/api-create.php?url=".$sUrl);
					break;
		case 2:		$sShortUrl = file_get_contents("http://is.gd/api.php?longurl=".$sUrl);
					break;
		case 3:		$sShortUrl = file_get_contents("http://api.bit.ly/shorten?version=2.0.1&longUrl=".$sUrl."&login=bitlyapidemo&apiKey=R_0da49e0a9118ff35f52f629d2d71bf07");
					$aShortUrl = json_decode($sShortUrl, true);
					$sShortUrl = $aShortUrl["results"][$sUrl]["shortUrl"];
					break;
		case 4:		$sShortUrl = file_get_contents("http://snipr.com/site/snip?r=simple&link=".$sUrl);
					break;
		case 5:		$sShortUrl = file_get_contents("http://zz.gd/api-create.php?url=".$sUrl);
					break;
	}
	
	return $sShortUrl;
}

function twittrup_admin_init() {

	add_action('publish_post',  'twittrup_tweet_save',5);
	
	if (function_exists('add_management_page')) {
		add_management_page('TwittrUp', 'TwittrUp', 8, __FILE__, 'twittrup_Twitter_manage_page');
	}
}
add_action('admin_menu','twittrup_admin_init');
?>
