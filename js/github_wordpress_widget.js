function github_wordpress_toggle(){
	if(document.getElementById('githublist').style.display == 'none') {
		document.getElementById('githubrepshow').innerHTML = 'Hide my repositories';
		document.getElementById('githublist').style.display = 'block';
	} else {
		document.getElementById('githubrepshow').innerHTML = 'Show my repositories';
		document.getElementById('githublist').style.display = 'none';
	}
}