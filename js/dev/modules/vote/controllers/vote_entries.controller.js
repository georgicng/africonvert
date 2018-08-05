function VoteEntriesController($scope, $state, $stateParams, contest, entries, VoteService, user) {
	
    if (contest.acf.media_type == "Video"){
        VoteService.getProjectStats(contest.wistia_project_id)
        .then(function(data){
            $scope.wistia_stat = data;
        })
        .catch(function(err){
            $scope.wistia = null;
        }); 
    }    
    
	$scope.data = {pageTitle: contest.title.rendered};
	$scope.logged_in = local_env.logged_in;
    $scope.entries = entries.data;
	$scope.entries_next_page = entries.next_page;
    $scope.entries_count = entries.count;
    $scope.getting_entries = false;
	$scope.post = contest;

    $scope.moreEntries = function() {
			if ($scope.getting_entries == true)
				return;
				$scope.getting_entries = true;

				VoteService.getEntries($stateParams.id, $scope.entries_next_page)
				.then(function(response){
					$scope.entries = $scope.entries.concat(response.data);
					$scope.entries_next_page = response.page;
					$scope.getting_entries = false;
				})
				.catch(function(err){
					//if(err == "Last")
						//$scope.entries_next_page = false;
						$scope.getting_entries = false;
				});
		}; 
	

}

angular
  .module('com.module.vote')
  .controller('VoteEntriesController',VoteEntriesController);