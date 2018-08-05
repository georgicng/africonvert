function VoteEntryController($scope, $stateParams, entry, VoteService, user, ngToast, $sce) {
        
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
                $scope.media = err;
            });
    }
    $scope.post = entry;

    /*$scope.vote = function () {
        var param = { contest: $scope.post.contest.ID, post: $stateParams.id };
        VoteService.save(
            param,
            function (response) {
                ngToast.create({
                    content: 'Voting successful...'
                });
                $scope.post.contest.can_vote = false;
                $scope.apply();
            },
            function (err) {
                ngToast.create({
                    content: 'Voting failed...'
                });
                $scope.apply();
            }
        );
    };*/

    $scope.getViewerURL = function(url) {
        if (url){
            url = "https://docs.google.com/gview?url=" + url + "&embedded=true";
            return $sce.trustAsResourceUrl(url);
        }        
    };

    $scope.error = function (err) {
        ngToast.create({
            verticalPosition: 'bottom',
            content: "Couldn't cast your vote"
        });
    }

    
    $scope.success = function (data) {
        $scope.post.contest.can_vote = false;
        ngToast.create({
            verticalPosition: 'bottom',
            content: "Voting successful"
        });
    }
    
}

angular
    .module('com.module.vote')
    .controller('VoteEntryController', VoteEntryController);