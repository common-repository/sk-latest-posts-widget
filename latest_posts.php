<?php
	/*
		Here you just have to put your forum address in $urlPath

		If you're having trouble editing this file you can ask for support here:
		http://forum.me2web.net/viewtopic.php?f=112&t=173

	*/


    // ENG: Forum home url address (starting with http://)
    // ITA: Indirizzo della home del forum (deve iniziare con http://)

    //   es. http://forum.me2web.net
    $urlPath = 'http://forum.me2web.net';










    /* Shouldn't have to edit below this */


    // Where your phpBB config.php file is located
    // Dove si trova il file config.php di phpbb3
    include 'config.php';



	$topicnumber = (isset($_GET['n']) && is_numeric($_GET['n']) && $_GET['n'] > 0)? ceil($_GET['n']) : 1;
	$forumIds = '';
	if(!empty($_GET['f'])){
		$fids = explode('-', $_GET['f']);
		$i = 0;
		foreach($fids as $fid){
			if(is_numeric($fid) && $fid > 0){
				$forumIds .= ($i > 0)? ' or ' : '';
				$forumIds .= 'f.forum_id = '.ceil($fid).' ';
				$i++;
			}
		}
		if($i > 1) $forumIds = ' ( '.$forumIds.' ) ';
		if($i > 0) $forumIds .= ' AND ';
	}


    $table_topics = $table_prefix. 'topics';
    $table_forums = $table_prefix. 'forums';
    $table_posts = $table_prefix. 'posts';
    $table_users = $table_prefix. 'users';
    $link = mysql_connect($dbhost, $dbuser, $dbpasswd) or die();
    mysql_select_db($dbname) or die();

    $query = "SELECT t.topic_id, t.topic_title, t.topic_last_post_id, t.forum_id, p.post_id, p.poster_id, p.post_time, u.user_id, u.username
    FROM $table_topics t, $table_forums f, $table_posts p, $table_users u
    WHERE t.topic_id = p.topic_id AND
    f.forum_id = t.forum_id AND
    $forumIds
    t.topic_status <> 2 AND
    p.post_id = t.topic_last_post_id AND
    p.poster_id = u.user_id
    ORDER BY p.post_id DESC LIMIT $topicnumber";
    $result = mysql_query($query) or die($query.mysql_error());

 	header ("content-type: text/xml");

    echo '<?xml version="1.0" encoding="UTF-8" ?>
<xml>
    	<topics>',"\n";
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    echo  '<topic>'."\n".'<url><![CDATA[',$urlPath,'/viewtopic.php?f=',$row['forum_id'],'&t=',$row['topic_id'],'&p=',$row['post_id'],'#p',$row['post_id'],']]></url>',"\n",
    	  '<title><![CDATA[',utf8_encode($row['topic_title']),']]></title>',"\n",
    	  '<profile><![CDATA[', $urlPath,'/memberlist.php?mode=viewprofile&u=',$row['user_id'],"]]></profile>\n",
    	  '<author><![CDATA[',$row['username'],']]></author>',"\n",
    	  '<date>',date('F j, Y, g:i a', $row['post_time']),"</date>\n</topic>";
    }
    echo "</topics>
    </xml>";
    mysql_free_result($result);
    mysql_close($link);
?>