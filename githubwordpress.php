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
		echo '<p><label for="' . $this->get_field_id("hidden") . '">Repo list is hidden by default:</label>';
		echo '<select id="' . $this->get_field_id("hidden") . '" name="' . $this->get_field_name("hidden") . '">';

		if ($instance['hidden'] == "0") {
			echo '<option value="0" selected="selected">No</option>';
			echo '<option value="1">Yes</option>';
		} else {
			echo '<option value="0">No</option>';
			echo '<option value="1" selected="selected">Yes</option>';
		}

		echo '</select>';
		echo '</div>';
	}

	function widget($args, $instance) {
		if ( isset($instance['error']) && $instance['error'] )
			return;

		if(isset($args['before_title']))
			$before_title = $args['before_title'];
		else
			$before_title = '<h3 class="widget-title">';
		
		if(isset($args['after_title']))
			$after_title = $args['after_title'];
		else
			$after_title = '</h3>';
		
		if(isset($args['before_widget']))
			$before_widget = $args['before_widget'];
		else
			$before_widget = '';
		
		if(isset($args['after_widget']))
			$after_widget = $args['after_widget'];
		else
			$after_widget = '';
		
		$user = $instance['username'];
		$url = "https://api.github.com/users/" . $user . "/repos";

		// set URL and other appropriate options
		$ch = curl_init();
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

		?>GitHub<?= $after_title; ?>
			<!-- octocat picture -->
			<div class="github_wordpress_image_holder"><img src="<?= plugins_url('/octocat_small.png', __FILE__); ?>" /></div>

			<a target="_blank" href="https://www.github.com/<?= $user; ?>"><?= $user; ?></a> @ <a target="_blank" href="https://www.github.com">Github</a>


			<?php if ($instance['hidden'] == "0") {
				echo '<p><a id="githubrepshow" onclick="javascript:github_wordpress_toggle();">Hide my repositories</a></p>';
				echo '<ul id="githublist">';
			} else {
				echo '<p><a id="githubrepshow" onclick="javascript:github_wordpress_toggle();">Show my repositories</a></p>';
				echo '<ul id="githublist" style="display: none;">';
			}

			foreach($json as $repo) {
				if (isset($json->message)) {
                                        echo 'GitHub API Error: ' . $json->message;
                                        break;
                                }

				echo '<li><a target="_blank" href="http://www.github.com/user/' . $repo->name . '">' . $repo->name . '</a><br />';

				$url = "https://api.github.com/repos/" . $user . "/" . $repo->name . "/commits";
				$repo_data = curl_exec($ch) or echo 'Error while fetching user stats!';
				$repo = json_decode($repo_data);
				$total = 0;
				$counter = 0;

				curl_setopt($ch, CURLOPT_URL, $url);

				foreach($repo as $coder) {
					$total++;
					if($coder->committer->login == $user)
						$counter++;
				}

				echo (int) (($counter / $total) * 100) . " percent of commits</li>";
				unset($coder);
			}

		curl_close($ch);
		echo "</ul>";
		echo $after_widget;
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;		
		$instance['username'] = strip_tags($new_instance['username']);
		$instance['hidden'] = strip_tags($new_instance['hidden']);
		return $instance;
	}		
	
}

add_action('widgets_init', create_function('', 'return register_widget("githubwordpress");'));
add_action("wp_head","github_add_scripts");
	
function github_add_scripts() {
	echo '<link rel="stylesheet" href="' . plugins_url("/css/github_wordpress_widget.css", __FILE__ ) . '" />';
	echo '<script type="text/javascript" language="javascript" src="' . plugins_url("/js/github_wordpress_widget.js", __FILE__ ) . '"></script>';
}
?>