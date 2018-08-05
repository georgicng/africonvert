angular
    .module('com.module.core')
    .directive('comment', function() {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: local_env.partials + 'comment.html',
            scope :{
                comment: '=',
                post: '='
            },
            controller: ['$scope', '$element', 'CommentService', function( $scope, $element, CommentService){
                $scope.loadChildren = function(){
                    CommentService.getChildren(comment.id)
                    .then(function(data){
                          $scope.children = data;
                    })
                    .catch(function(err){
                          $scope.children = null;
                    });
                };
            }]
        };
    });