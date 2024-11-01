=== Sk Latest Post Widget ===
Contributors: Skipstorm
Tags: phpbb, phpbb3, crosspost, forum, latest posts
Requires at least: 2.8
Tested up to: 2.8.4
Stable tag: trunk

This plugins adds a widget with the latest posts from your forum.

== Description ==

This plugins adds a widget with the latest posts from your phpbb3 forum.
To make it work you have to add a file on your forum directory wich is easily configurable to fetch the posts from a specific category or the whole forum.
This plugins supports multiple istances so you can display topics from different forums or categories in multiple widgets.


== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Copy latest_posts.php in your phpbb3 forum root directory
4. Change the value of $urlPath with your forum address in latest_posts.php, rename the file if you want.
5. Place the widget in your sidebar and customize it from the 'Widget' menu in WordPress. Insert the complete url of the latest_posts.php file or however you called it.

You can add as many widgets as you want.
Limit the number of posts or forum ids right from the widget admin panel.

This version does not recurse subforums, you'll have to add all the ids.

If you need support visit the plugin homepage.

= Customizing the CSS =
just add a class for sk_latest_posts_widget in you css file.
