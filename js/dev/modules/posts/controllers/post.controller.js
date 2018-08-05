function PostController($scope, $rootScope,  $state, $stateParams, post, PostService, $compile) {
	if($rootScope.user){	
		//deal with login as well	
		//$scope.user = user;
	}

	$scope.loadComments = function (){ 
		var directive = '<comments post="post.id"></comments>';      
        var commentsFactory = $compile(directive);
        var template = commentsFactory($scope);
        var containerDiv = document.getElementById('comments');
        angular.element(containerDiv).append(template);
		$scope.comment_active = true;
    }
	
	$scope.comment_active = false;
	$scope.post = post;

	
}

angular
    .module('com.module.posts')
    .controller('PostController', PostController);