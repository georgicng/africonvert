function PageController($scope, page) {
        $scope.post = page;	        
}
angular
    .module('com.module.pages')
    .controller('PageController', PageController);