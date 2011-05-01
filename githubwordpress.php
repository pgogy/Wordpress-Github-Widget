<?php
/*
Plugin Name: Github Wordpress Widget
Plugin URI: http://www.pgogy.com/code/githubwordpress
Description: A widget for displaying github profiles
Version: 0.9
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
		echo '<p><label for="' . $this->get_field_id("password") .'">GitHub Password:</label>';
		echo '<input type="password" name="' . $this->get_field_name("password") . '" '; 
		echo 'id="' . $this->get_field_id("password") . '" value="' . $instance["password"] . '" /></p>';
		echo '</div>';
	}

	function widget($args, $instance) {
		extract($args);		
		echo $before_widget;
		
		
		
		// create a new cURL resource
		$ch = curl_init();
		
		$user = $instance['username'];
		$password = $instance['password'];
		
		$url = "http://github.com/api/v2/xml/repos/show/" . $user;
	
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($user . ":" . $password));
		
		
		// grab URL and pass it to the browser
		$data = curl_exec($ch);
		
		$xml = new SimpleXMLElement($data);
	
		?><li><h3 class="widget-title">GitHub</h3>
			<div style="text-align:center">
			<p style="padding:0px; margin:0px;">
				<img src="<?PHP
				
					echo plugins_url('/octocat_small.png', __FILE__);
					
				?>" />
			</p>
			<p style="padding:0px; margin:0px;">
				<a target="_blank" href="https://www.github.com/<?PHP
				
					echo $user;
				
				?>"><?PHP
				
					echo $user;
				
				?></a> @ <a target="_blank" href="https://www.github.com">Github</a>
			</p>
			<p>
				<a style="cursor:hand; cursor:pointer" onclick="javascript:if(document.getElementById('githublist').style.display!='block'){document.getElementById('githublist').style.display='block';}else{document.getElementById('githublist').style.display='none';};">Show repositories</a>
			</p></div>
			<div id="githublist" style="text-align:left; display:none">
			<?PHP	
				
		foreach($xml->repository as $repo){
		
			echo "<ul>";
		
			echo "<li><a target=\"_blank\" href=\"http://www.github.com/$user/$repo->name\">$repo->name</a><br />";
			$url = "http://github.com/api/v2/xml/repos/show/" . $user . "/" . $repo->name . "/contributors";
			curl_setopt($ch, CURLOPT_URL, $url);
			$repo_data = curl_exec($ch);
			
			$repo_xml = new SimpleXMLElement($repo_data);
	
			$total = 0;
			$counter = 0;
			
			foreach($repo_xml->contributor as $coder){
			
				$total += $coder->contributions;
				
				if($coder->login==$user){
			
					echo $coder->contributions . " contributions <br />";
					$counter = $coder->contributions;
					
				}	
			
			}	
			
			echo (int)(($counter/$total)*100) . " percent of total <br />Languages $repo->language</li></ul>";
		
		}
		
		echo "<li>";	
		
		curl_close($ch);
				
		echo "</div>";
		
		echo $after_widget;
		
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;		
		$instance['username'] = strip_tags( $new_instance['username'] );
		$instance['password'] = strip_tags( $new_instance['password'] );	
		return $instance;
	}		
	
} 

add_action('widgets_init', create_function('', 'return register_widget("githubwordpress");'));

?>
