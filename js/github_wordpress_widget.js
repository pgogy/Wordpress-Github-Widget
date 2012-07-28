function github_wordpress_toggle(){

	if(document.getElementById('githublist').style.display!='block'){
		document.getElementById('githublist').style.display='block';
		document.getElementById('githubrepshow').innerHTML = 'Hide my repositories';
	}else{
	    document.getElementById('githublist').style.display='none';
		document.getElementById('githubrepshow').innerHTML = 'Show my repositories';
	}		
															
}