function SingleController($scope, $state, $stateParams, contest, ArchiveService, user) {
	$scope.entries;
	$scope.post = contest;
	$scope.getEntries = function(){
		if ($scope.entries == undefined){
			console.log('called');
			ArchiveService.getEntries($stateParams.id)
			.then(function(entries){
				console.log(entries);
				$scope.entries = entries;
				$scope.$apply();
			})
			.catch(function(error){
				console.log('error');
				$scope.$apply();
			});
		}
		
	}

}

angular
  .module('com.module.archive')
  .controller('SingleController',SingleController);