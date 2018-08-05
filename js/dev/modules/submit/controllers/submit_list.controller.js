function SubmitListController($scope, contests, $stateParams, moment, SubmitService) {
	$scope.totalItems =  SubmitService.getTotalPost();
    $scope.currentPage = $stateParams.page;

    $scope.setPage = function (pageNo) {          
        $state.go('app.submitItems', {page: pageNo});
    };

    $scope.pageChanged = function() {
        $state.go('app.submitItems', {page: $scope.currentPage});
    };
	$scope.data = {pageTitle: "Contests"};
	$scope.data.posts = contests;

    $scope.getGuageText = function (obj){
        $output = ''
        switch(obj.status){
            case 'past':
             $output = 'SUBMISSION CLOSED'; 
             break;
            case 'future':
             $output = 'SUBMISSION HAS NOT OPENED'; 
             break;
            case 'running':
             $output = 'SUBMISSION CLOSES IN ' + obj.deadline + ' DAYS'; 
             break;
            case 'closing':
             $output = 'SUBMISSION CLOSES TODAY'; 
             break;   
        }
        return $output;
	};

    $scope.getButtonLabel = function (obj){
        $output = ''
        switch(obj.status){
            case 'past':
             $output = 'VIEW PROGRESS'; 
             break;
            case 'future':
             $output = 'LEARN MORE'; 
             break;
            case 'running':
            case 'closing':
             $output = 'ENTER NOW'; 
             break;    
        }
        return $output;
	};

}

angular
  .module('com.module.submit')
  .controller('SubmitListController',SubmitListController);
