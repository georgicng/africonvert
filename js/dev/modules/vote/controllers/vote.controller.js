function VoteController($scope, contests, VoteService, $stateParams, moment) {
	$scope.totalItems =  VoteService.getTotalPost();
    $scope.currentPage = $stateParams.page;

    $scope.setPage = function (pageNo) {          
        $state.go('app.submitItems', {page: pageNo});
    };

    $scope.pageChanged = function() {
        //code to get the next collection        
        $state.go('app.submitItems', {page: $scope.currentPage});
    };
	$scope.data = {pageTitle: "Contests"};
	$scope.data.posts = contests;

    $scope.getGaugeText = function (obj){
        $output = ''
        switch(obj.status){
            case 'past':
             $output = 'VOTING CLOSED'; 
             break;
            case 'future':
             $output = 'VOTING HAS NOT OPENED'; 
             break;
            case 'running':
             $output = 'VOTING CLOSES IN ' + obj.deadline + ' DAYS'; 
             break;
            case 'closing':
             $output = 'VOTING CLOSES TODAY'; 
             break;   
        }
        return $output;
	};

    $scope.getButtonLabel = function (obj){
        $output = ''
        switch(obj.status){
            case 'past':
             $output = 'VIEW RESULTS'; 
             break;
            case 'future':
             $output = 'LEARN MORE'; 
             break;
            case 'running':
            case 'closing':
             $output = 'VOTE NOW'; 
             break;   
        }
        return $output;
	};
}

angular
  .module('com.module.vote')
  .controller('VoteController',VoteController);
