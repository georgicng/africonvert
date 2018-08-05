function WatchListController($scope, $state, $stateParams, $log, entries, categories, WatchService) {
	$scope.data = {pageTitle: "Watch"};
    $scope.defaults = $stateParams;
	$scope.data.posts = entries;
    $scope.contests = categories;
    
    $scope.pagination = {
        page: parseInt($stateParams.page),
        totalItems: WatchService.getTotalPost()
    };

    $scope.filterContent = function(filter){
        console.log(filter);
        filter.page =  1;
        $state.go('app.watchlist', filter, {reload:true});
    };

    /*var setPage = function (pageNo) {       
        $stateParams.page =  pageNo;     
        $state.go('app.watchlist', $stateParams);
    };*/

    $scope.pageChanged = function() {
        $stateParams.page =  $scope.pagination.page;     
        $state.go('app.watchlist', $stateParams, {reload:true});
    };

     $scope.getIcon = function(type) {
        //code to get the next collection  
        switch(type){
            case "Video":
                return "glyphicon-play-circle";
            case "Audio":
                return "glyphicon-music";
            case "Image":
                return "glyphicon-picture";
            case "PdfDocument":
                return "glyphicon-file";
            case "MicrosoftOfficeDocument":
                return "glyphicon-file";
            default:
                return "glyphicon-eye-open";
        }
    };
}

angular
    .module('com.module.watch')
    .controller('WatchListController', WatchListController);
