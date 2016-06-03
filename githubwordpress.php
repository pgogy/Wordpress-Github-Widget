<?php
/*
Plugin Name: Github Wordpress Widget
Description: A widget for displaying github profiles
Version: 1.2
Author: Pgogy
Author URI: http://www.pgogy.com
License: GPL2
*/

class githubwordpress extends WP_Widget {
	function githubwordpress() {
		
		require(dirname(__FILE__) . "/languages/" . get_bloginfo('language') . "/index.php");
	
		$options = array( 'classname' => 'githubwordpress', 'description' => __( $github_description ) );
		$this->WP_Widget('githubwordpress', __($github_name), $options);
	}
	
	function form($instance) {
		
		if(file_exists(dirname(__FILE__) . "/languages/" . get_bloginfo('language') . "/index.php")){
		
			require(dirname(__FILE__) . "/languages/" . get_bloginfo('language') . "/index.php");
			
		}else{
		
			require(dirname(__FILE__) . "/languages/en-US/index.php");
		
		}
		
		if(!isset($instance["title"])){
			$instance["title"] = "";
		}
		
		if(!isset($instance["username"])){
			$instance["username"] = "";
		}
		
		if(!isset($instance["password"])){
			$instance["password"] = "";
		}
		
		echo '<div id="githubwordpress-widget-form">';
		echo '<p><label for="' . $this->get_field_id("title") .'">' . __("Title") . ' :</label>';
		echo '<input type="text" name="' . $this->get_field_name("title") . '" ';
		echo 'id="' . $this->get_field_id("username") . '" value="' . $instance["title"] . '" /></p>';
		echo '<p><label for="' . $this->get_field_id("username") .'">' . $github_username . ' :</label>';
		echo '<input type="text" name="' . $this->get_field_name("username") . '" ';
		echo 'id="' . $this->get_field_id("username") . '" value="' . $instance["username"] . '" /></p>';
		echo '<p><label for="' . $this->get_field_id("username") .'">' . $github_password . ' :</label>';
		echo '<input type="password" name="' . $this->get_field_name("password") . '" ';
		echo 'id="' . $this->get_field_id("password") . '" value="' . $instance["password"] . '" /></p>';
		echo "<p>" . $github_warning . "</p>";
		echo '<p><label for="' . $this->get_field_id("hidden") . '">' . $github_repo . ':</label>';
		echo '<select id="' . $this->get_field_id("hidden") . '" name="' . $this->get_field_name("hidden") . '">';

		if ($instance['hidden'] == "0") {
			echo '<option value="0" selected="selected">' . $github_no . '</option>';
			echo '<option value="1">' . $github_yes . '</option>';
		} else {
			echo '<option value="0">' . $github_no . '</option>';
			echo '<option value="1" selected="selected">' . $github_yes . '</option>';
		}

		echo '</select>';
		echo '</div>';
	}

	function widget($args, $instance) {
	
		require(dirname(__FILE__) . "/languages/" . get_bloginfo('language') . "/index.php");
	
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
		$password = $instance['password'];
		
		if(!empty($password)){
		
			$headers = array(
				"Authorization: Basic " . base64_encode( $user . ":" . $password)
			);
			
		}
		
		$url = "https://api.github.com/users/" . $user . "/repos";
		
		// set URL and other appropriate options
		$ch = curl_init();
		$vers = curl_version();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, 'curl/' . $vers['version'] );
		
		if(!empty($password)){
		
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			
		}
		
		// grab URL and pass it to the browser
		$data = curl_exec($ch);
		
		curl_setopt($ch, CURLOPT_URL, $url);
		$data = curl_exec($ch);
		$json = json_decode($data);
		
		echo $before_widget;
		echo $before_title;
		echo $instance['title'];
		echo $after_title; 
		?>
			<!-- octocat picture -->
			<div class="github_wordpress_image_holder"><img src="<?= plugins_url('/octocat_small.png', __FILE__); ?>" /></div>
			<script type="text/javascript">
				function github_wordpress_toggle(){
					if (jQuery("#githublistdiv").is(":hidden")) {
						document.getElementById('githubrepshow').innerHTML = '<?= $github_hide_string ?>';
					} else {
						document.getElementById('githubrepshow').innerHTML = '<?= $github_show_string ?>';
					}
					jQuery('#githublistdiv').slideToggle('slow');
				}
			</script>

			<a target="_blank" href="https://www.github.com/<?= $user; ?>"><?= $user; ?></a> @ <a target="_blank" href="https://www.github.com">Github</a>
			<p><a id="githubrepshow" onclick="javascript:github_wordpress_toggle();">

			<?php if ($instance['hidden'] == "0") {
				echo $github_hide_string . '</a></p>';
				echo '<div id="githublistdiv"><ul id="githublist">';
			} else {
				echo $github_show_string . '</a></p>';
				echo '<div id="githublistdiv" style="display:none"><ul id="githublist">';
			}
			
			foreach($json as $repo) {
				if (isset($json->message)) {
                                        echo $github_error . " " . $json->message;
                                        break;
                                }

				echo '<li><a target="_blank" href="http://www.github.com/' . $user . '/' . $repo->name . '">' . $repo->name . '</a><br />';

				$url = "https://api.github.com/repos/" . $user . "/" . $repo->name . "/commits";
				curl_setopt($ch, CURLOPT_URL, $url);
				$repo_data = curl_exec($ch);
				$repo = json_decode($repo_data);
				$total = 0;
				$counter = 0;

				foreach($repo as $coder) {
					$total++;
					
					if($coder->committer->login == $user)
						$counter++;
				}

				if($counter==0){
				
					echo  "0 " . $github_percent_string . "</li>";
				
				}else{

					echo (int) (($counter / $total) * 100) . " " . $github_percent_string . "</li>";
					
				}
				unset($coder);
			}
		
		curl_close($ch);
		echo "</ul></div>";
		echo $after_widget;
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;		
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['username'] = strip_tags($new_instance['username']);
		$instance['password'] = strip_tags($new_instance['password']);
		$instance['hidden'] = strip_tags($new_instance['hidden']);
		return $instance;
	}		
	
}

add_action('widgets_init', create_function('', 'return register_widget("githubwordpress");'));
	
function themeslug_enqueue_style() {
	wp_enqueue_style( 'github-profile-display', plugins_url("/css/github_wordpress_widget.css", __FILE__ ), false ); 
	wp_enqueue_script( 'jquery' );
}

add_action( 'wp_enqueue_scripts', 'themeslug_enqueue_style' );	
