function WatchItemController($scope, $stateParams, $sce, entry, VoteService) {
    $scope.getViewerURL = function(url){
        console.log(url);
        if (url){
            url = "https://docs.google.com/gview?url=" + url + "&embedded=true";
            return $sce.trustAsResourceUrl(url);
        }        
    };

	if (entry.acf.wistia_id) {
        VoteService.getMediaStats(entry.acf.wistia_id)
            .then(function (data) {
                $scope.wistia = data;
            }).catch(function (err) {
                $scope.wistia = null;
            });
    }
	if (entry.acf.type !== "Video") {
        VoteService.getMedia(entry.acf.wistia_hash)
            .then(function (data) {
                $scope.media = data.assets[0];
            }).catch(function (err) {
                $scope.media = null;
            });
    }
    $scope.post = entry;

}
                              
angular
    .module('com.module.watch')
    .controller('WatchItemController', WatchItemController);