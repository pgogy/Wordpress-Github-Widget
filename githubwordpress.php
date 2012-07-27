<?php
/*
Plugin Name: Github Wordpress Widget
Plugin URI: http://www.pgogy.com/code/githubwordpress
Description: A widget for displaying github profiles
Version: 0.94
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
		extract($args);	
		
		$data = explode("</li>",$before_widget);			
		
		echo $data[0];
		
		// create a new cURL resource
		$ch = curl_init();
		
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
	
		?><h3 class="widget-title">GitHub</h3>
			<div style="padding:0px; margin:0px;">
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
				
			<div style="padding-bottom: 20px;">	
			<!-- expand block open -->
				<a style="cursor:hand; cursor:pointer" onclick="javascript:if(document.getElementById('githublist').style.display!='block'){document.getElementById('githublist').style.display='block';document.getElementById('githubrepshow').innerHTML = 'Hide my repositories';}else{document.getElementById('githublist').style.display='none';document.getElementById('githubrepshow').innerHTML = 'Show my repositories'};">Show my repositories</a>
            <!-- expand block closes -->
			</div>
			<ul id="githublist" style="text-align:left; display:none">
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
				
		echo "</ul></div>";
		
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;		
		$instance['username'] = strip_tags( $new_instance['username'] );
		return $instance;
	}		
	
} 

add_action('widgets_init', create_function('', 'return register_widget("githubwordpress");'));

?>
