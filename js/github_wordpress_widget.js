function github_wordpress_toggle(){
	if (jQuery("#githublistdiv").is(":hidden")) {
		document.getElementById('githubrepshow').innerHTML = 'Hide my repositories';
	} else {
		document.getElementById('githubrepshow').innerHTML = 'Show my repositories';
	}
	jQuery('#githublistdiv').slideToggle('slow');
}