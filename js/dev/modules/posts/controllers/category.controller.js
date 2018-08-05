function CategoryController($scope, $stateParams, PostService) {
	$scope.data = {};
	PostService.query({param1:'posts', _embed:true, categories:$stateParams.id}, function(data){
		if (!data) {
			document.querySelector('title').innerHTML = 'Category not found | Afriflow';
			$scope.data.pageTitle = 'Category not found';
		} else {
			$scope.current_category_id = $stateParams.id;
			$scope.data.posts = data;
		}
	});
}
                            
angular
    .module('com.module.posts')
    .controller('CategoryController', CategoryController);