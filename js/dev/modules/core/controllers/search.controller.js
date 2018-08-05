function SearchController($scope, $stateParams, matches, SearchService) {
    
    $scope.pagination = {
        totalItems: matches.total,
        currentPage:1
    };

    $scope.setPage = function (pageNo) {
        $scope.pagination.currentPage = pageNo;
    };

    $scope.pageChanged = function() {
         SearchService.get({term:$stateParams.term, page:$scope.pagination.currentPage})
         .then(function(data){
            $scope.posts = data.results;
         })
         .catch(function(err){
            //do toast
         });
    };

	$scope.getPath = function(item) {
        //code to generate path from serp object
        return '/home';
    };

    $scope.getContestPath = function(item) {
        var path = "";
        var id = item.id;

        switch(item.acf.stage){
            case 'Submit':
                path = "submit/";
            case 'Vote':
                path = "vote/";
            case 'Complete':
                path = "archive/";
            default:
                path = "submit/";
        }
        return path + id;
    };

    $scope.getItemCount = function(item) {
        var items = $scope.posts.filter(function(record){
            return record.type == item;
        });
        return items.length;
    };

	$scope.posts = matches.results;
    $scope.term = $stateParams.term.replace('+', ' ');
}

angular
    .module('com.module.core')
    .controller('SearchController', SearchController); 