<?php
/*
Plugin Name: Github Wordpress Widget
Plugin URI: http://www.pgogy.com/code/githubwordpress
Description: A widget for displaying github profiles
Version: 0.95
Author: Pgogy
Author URI: http://www.pgogy.com
License: GPL2
*/

class githubwordpress extends WP_Widget {

	function githubwordpress() {
		$options = array( 'classname' => 'githubwordpress', 'description' => __( "A widget for displaying github profiles." ) );
		$this->WP_Widget('githubwordpress', __('Github Profile'), $options);
	}
	
	function form($instance) {		
	
		echo '<div id="githubwordpress-widget-form">';		
		echo '<p><label for="' . $this->get_field_id("username") .'">GitHub Username:</label>';
		echo '<input type="text" name="' . $this->get_field_name("username") . '" '; 
		echo 'id="' . $this->get_field_id("username") . '" value="' . $instance["username"] . '" /></p>';
		echo '</div>';
	}

	function widget($args, $instance) {
		
		if ( isset($instance['error']) && $instance['error'] )
			return;

		// create a new cURL resource
		$ch = curl_init();
		
		if(isset($args['before_title'])){
		
			$before_title = $args['before_title'];
		
		}else{
		
			$before_title = '<h3 class="widget-title">';
		
		}
		
		if(isset($args['after_title'])){
		
			$after_title = $args['after_title'];
		
		}else{
		
			$after_title = '</h3>';
		
		}
		
		if(isset($args['before_widget'])){
		
			$before_widget = $args['before_widget'];
		
		}else{
		
			$before_widget = '';
		
		}
		
		if(isset($args['after_widget'])){
		
			$after_widget = $args['after_widget'];
		
		}else{
		
			$after_widget = '';
		
		}
		
		$user = $instance['username'];
		
		$url = "https://api.github.com/users/" . $user . "/repos";
	
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		
		// grab URL and pass it to the browser
		$data = curl_exec($ch);
	
		$json = json_decode($data);
	
		echo $before_widget;
		echo $before_title;
	
		?>GitHub<?PHP echo $after_title; ?>
			<div class="github_wordpress_image_holder">
			<!-- octocat picture block open -->
			  <img src="<?PHP
				
				echo plugins_url('/octocat_small.png', __FILE__);
					
			  ?>" />
			<!-- octocat picture block closes -->
			</div>
				
			<a target="_blank" href="https://www.github.com/<?PHP
				
				echo $user;
					
			?>"><?PHP
				
				echo $user;
				
			?></a> @ <a target="_blank" href="https://www.github.com">Github</a>
				
			<p>	
			<!-- expand block open -->
				<a id="githubrepshow" onclick="javascript:github_wordpress_toggle();">Show my repositories</a>
            <!-- expand block closes -->
			</p>
			<ul id="githublist" style="display: none;">
			<?PHP	
				
		foreach($json as $repo){
		
			echo "<li><a target=\"_blank\" href=\"http://www.github.com/$user/$repo->name\">$repo->name</a><br />";
			$url = "https://api.github.com/repos/" . $user . "/" . $repo->name . "/commits";		
			
			curl_setopt($ch, CURLOPT_URL, $url);
			$repo_data = curl_exec($ch);
			
			$repo = json_decode($repo_data);
	
			$total = 0;
			$counter = 0;
			
			foreach($repo as $coder){
			
				$total++;
				
				if($coder->committer->login==$user){
			
					$counter++;
					
				}	
			
			}	
			
			echo (int)(($counter/$total)*100) . " percent of commits</li>";
		
		}
		
		curl_close($ch);
				
		echo "</ul>";
		
		echo $after_widget;
		
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;		
		$instance['username'] = strip_tags( $new_instance['username'] );
		return $instance;
	}		
	
} 

add_action('widgets_init', create_function('', 'return register_widget("githubwordpress");'));

add_action("wp_head","github_add_scripts");		
	
function github_add_scripts(){
	
	?><link rel="stylesheet" href="<?PHP echo plugins_url("/css/github_wordpress_widget.css" , __FILE__ ); ?>" />
	<script type="text/javascript" language="javascript" src="<?PHP echo plugins_url("/js/github_wordpress_widget.js" , __FILE__ ); ?>"></script><?PHP
	
}

?>