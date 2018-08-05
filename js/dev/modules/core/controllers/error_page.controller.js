function Error_PageController() {
	document.querySelector('title').innerHTML = 'Page not found | AngularJS Demo Theme';
}

angular
    .module('com.module.core')
    .controller('404', Error_PageController);
