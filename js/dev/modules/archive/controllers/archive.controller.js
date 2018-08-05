function ArchiveController($scope, contests, $stateParams, ArchiveService) {
	$scope.totalItems =  ArchiveService.getTotalPost();
    $scope.currentPage = $stateParams.page;

    $scope.setPage = function (pageNo) {          
        $state.go('app.submitItems', {page: pageNo});
    };

    $scope.pageChanged = function() {
        //code to get the next collection        
        $log.log('Page changed to: ' + $scope.currentPage);
        $state.go('app.submitItems', {page: $scope.currentPage});
    };
	$scope.data = {pageTitle: "Contests"};
	$scope.data.posts = contests;
	
}

angular
  .module('com.module.archive')
  .controller('ArchiveController',ArchiveController);
