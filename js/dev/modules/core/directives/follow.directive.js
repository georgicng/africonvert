angular
    .module('com.module.core')
    .directive('follow', function() {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: local_env.partials + 'follow.html',
            scope :{
                author: '=',
                following: '='
            },
            controller: ['$scope', '$element', 'ngToast', 'MemberService', function( $scope, $element, ngToast, MemberService ){

                $scope.follow = function(){
                    MemberService.followUser($scope.author)
                    .then(function(data){
                        $scope.following = true;
                        ngToast.create({
                            verticalPosition: 'bottom',
                            content: "You're now folowing..."
                        });  
                    })
                    .catch(function(err){
                    });
                };

                $scope.unfollow = function(){
                    MemberService.unfollowUser($scope.author)
                    .then(function(data){
                        $scope.following = false;
                        ngToast.create({
                            verticalPosition: 'bottom',
                            content: "Ohh, but why..."
                        });
                    })
                    .catch(function(err){
                    });
                };
            }]
        };
    });