angular
    .module('com.module.core')
    .directive('comments', function(env) {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: env.partials + 'comments.html',
            scope :{
                post: '='
            },
            controller: ['$scope', '$element', 'CommentService', function( $scope, $element, CommentService){
                $scope.comments = undefined;
                $scope.getting_comments = false;
                $scope.comment_next_page = null;
                CommentService.getComments($scope.post)
                .then(function(response){
                    $scope.comments = response.data;
                    $scope.comment_next_page = response.page;
                })
                .catch(function(err){
                    $scope.comments = null;
                    $scope.comment_next_page = false;
                });

                 
                $scope.moreComments = function() {
                    if ($scope.getting_comments == true)
                        return;
                        $scope.getting_comments = true;

                        CommentService.getComments($scope.post, $scope.comment_next_page)
                        .then(function(response){
                            $scope.comments = $scope.comments.concat(response.data);
                            $scope.comment_next_page = response.page;
                            $scope.getting_comments = false;
                        })
                        .catch(function(err){
                            if(err == "Last")
                                $scope.comment_next_page = false;
                            $scope.getting_comments = false;
                        });
                };             
                
            }]
        };
    });