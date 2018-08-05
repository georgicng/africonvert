angular
    .module('com.module.core')
    .directive('like', function() {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: local_env.partials + 'like.html',
            scope :{
                post: '=',
                user: '=',
                data: '='
            },
            controller: ['$scope', '$element', 'ngToast', 'LikeService', function( $scope, $element, ngToast, LikeService ){
                $scope.dislike = function(){
                    LikeService.save({
                        id: $scope.post,
                        type: 'dislike'	
                    })
                    .$promise.then(function(res){	
                            $scope.data = res.data;
                            ngToast.create({
                                verticalPosition: 'bottom',
                                content: 'Ohh, but why...'
                            });  
                    })
                    .catch(function(res){
                        $scope.data = res.data;
                    });
                };

                $scope.like = function(){
                    LikeService.save({
                        id: $scope.post,
                        type: 'like'
                    })
                    .$promise.then(function(res){			
                        
                            $scope.data = res.data;
                            ngToast.create({
                                verticalPosition: 'bottom',
                                content: 'Awww, we like you too...'
                            });
                    })
                    .catch(function(res, status){
                        $scope.data = res.data;
                    });
                }
            }]
        };
    });