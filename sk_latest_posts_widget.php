<?php
/*
Plugin Name: Sk Latest Posts Widget
Plugin URI: http://forum.me2web.net/viewtopic.php?f=112&t=173
Description: This plugins adds a widget with the latest posts from your forum.
Author: Skipstorm
Version: 1.2
Author URI: http://www.skipstorm.org/
*/



add_action( 'widgets_init', 'sk_latest_posts_widget_load_widget' );


function sk_latest_posts_widget_load_widget() {
	register_widget( 'Sk_Latest_Posts_Widget' );
}

class Sk_Latest_Posts_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function Sk_Latest_Posts_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'sk_latest_posts_widget', 'description' => __('Shows the latest post of a forum in a widget.', 'sk_latest_posts_widget') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 350, 'height' => 350, 'id_base' => 'sk_latest_posts_widget-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'sk_latest_posts_widget-widget', __('sk_latest_posts_widget', 'sk_latest_posts_widget'), $widget_ops, $control_ops );
	}


    function widget($args, $instance) {
		extract($args);

        global $wp_query,$wpdb,$wp_rewrite;

		$title = apply_filters('widget_title', $instance['title'] );
		$url = $instance['url'];
        $forumids = $instance['forumids'];
        $numberposts = $instance['numberposts'];
        $beforename = $instance['beforename'];
        $beforedate = $instance['beforedate'];
        $displayDate = $instance['date'];
        $displayAuthor = $instance['author'];

        if(!empty($numberposts) && $numberposts > 0 && !empty($forumids)){
            $url .= '?n='.ceil($numberposts).'&f='.$forumids;
        } else if(!empty($numberposts) && $numberposts > 0){
            $url .= '?n='.ceil($numberposts);
        } else if(!empty($forumids)){
            $url .= '?&f='.$forumids;
        }

        $http = new WP_Http();
        @$page = $http->request($url);

        if($page && !empty($page['body'])){
            $xml = new SimpleXMLElement($page['body']);

            echo $before_widget.$before_title.$title.$after_title;
            $limit = ($numberposts > 0)? $numberposts : count($xml->topics->topic);
            echo '<ul class="sk_latest_posts_widget">';
            for($i = 0; $i < $limit; $i++){
                echo '<li><p><a href="',$xml->topics->topic[$i]->url,'">',$xml->topics->topic[$i]->title,'</a></p>',
                (($displayAuthor)? $beforename.'<a href="'.$xml->topics->topic[$i]->profile.'">'.$xml->topics->topic[$i]->author.'</a> ' : ''),
                (($displayDate)? $beforedate.$xml->topics->topic[$i]->date : ''),'</li>';
            }
            echo '</ul>';
            echo $after_widget;
        }
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['url'] = $new_instance['url'];
        $forumIds = '';
        if(!empty($new_instance['forumids'])){
            $fids = explode('-', $new_instance['forumids']);
            $i = 0;
            foreach($fids as $fid){
                if(is_numeric($fid) && $fid > 0){
                    $forumIds .= ($i > 0)? '-' : '';
                    $forumIds .= ceil($fid);
                    $i++;
                }
            }
        }
		$instance['forumids'] = $forumIds;
		$instance['beforename'] = $new_instance['beforename'];
		$instance['beforedate'] = $new_instance['beforedate'];
		$instance['numberposts'] = (is_numeric($new_instance['numberposts']) && $new_instance['numberposts'] > 0)? $new_instance['numberposts'] : 1;
		$instance['date'] = $new_instance['date'];
		$instance['author'] = $new_instance['author'];

		return $instance;
	}

	function form( $instance ) {


		/* Set up some default widget settings. */
        $defaults = array( 'title' => __('sk_latest_posts_widget', 'sk_latest_posts_widget')
            , 'url' => __('http://forum.me2web.net/latest_posts.php', 'sk_latest_posts_widget')
            , 'forumids' => __('', 'sk_latest_posts_widget')
            , 'beforename' => __('by: ', 'sk_latest_posts_widget')
            , 'beforedate' => __('on: ', 'sk_latest_posts_widget')
            , 'numberposts' => __('5', 'sk_latest_posts_widget')
            , 'date' => __(true, 'date')
            , 'author' => __(true, 'author'));
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'sk_latest_posts_widget'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e('Latest posts file url:', 'sk_latest_posts_widget'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" value="<?php echo $instance['url']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'forumids' ); ?>"><?php _e('Limit to forum ids (separed by - like 1-25-42, empty = all):', 'sk_latest_posts_widget'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'forumids' ); ?>" name="<?php echo $this->get_field_name( 'forumids' ); ?>" value="<?php echo $instance['forumids']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'numberposts' ); ?>"><?php _e('Max Posts:', 'sk_latest_posts_widget'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'numberposts' ); ?>" name="<?php echo $this->get_field_name( 'numberposts' ); ?>" value="<?php echo $instance['numberposts']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'beforedate' ); ?>"><?php _e('Before date:', 'sk_latest_posts_widget'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'beforedate' ); ?>" name="<?php echo $this->get_field_name( 'beforedate' ); ?>" value="<?php echo $instance['beforedate']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'date' ); ?>"><?php _e('Display date:', 'sk_latest_posts_widget'); ?></label>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'date' ); ?>" name="<?php echo $this->get_field_name( 'date' ); ?>" <?php echo ($instance['date'])? 'checked="checked"': ''; ?> style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'beforename' ); ?>"><?php _e('Before author:', 'sk_latest_posts_widget'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'beforename' ); ?>" name="<?php echo $this->get_field_name( 'beforename' ); ?>" value="<?php echo $instance['beforename']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'author' ); ?>"><?php _e('Display author link:', 'sk_latest_posts_widget'); ?></label>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'author' ); ?>" name="<?php echo $this->get_field_name( 'author' ); ?>" <?php echo ($instance['author'])? 'checked="checked"': ''; ?> style="width:100%;" />
		</p>

	<?php
	}
}

?>