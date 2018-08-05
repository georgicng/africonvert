angular
    .module('com.module.core')
    .directive('commentForm', function() {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: local_env.partials + 'comment_form.html',
            scope :{
                post: '=',
                parent: '=',
                toggle: '='
            },
            controller: ['$scope', '$element', 'ngToast', 'CommentService', 'Auth', function( $scope, $element, ngToast, CommentService, Auth ){
                var beforeSubmit = function(){
                    $scope.disable = false;
                    $scope.submitButtonText = $scope.parent !== undefined? "Reply" : "Comment"
                };

                var processSubmit = function(){
                    $scope.disable = true;
                    $scope.submitButtonText = "Processing";
                };

                var afterSubmit = function(){
                    beforeSubmit(); 
                    $scope.toggle = !$scope.toggle;
                };
                
                $scope.isLoggedIn = function(){
                   return Auth.isLoggedIn();
                };

                $scope.savecomment = function(openComment){
                    processSubmit();
                    openComment.post = $scope.post;
                    if (parent){
                        openComment.parent = $scope.parent;
                    }
                    if (Auth.isLoggedIn()){
                        openComment.author = Auth.getUserId();
                    }
                    CommentService.saveComment(openComment)
                    .then(function(response){
                        $scope.openComment = {};
                        ngToast.create({
                                content: 'Comment successfully tendered for moderation...'
                        }); 
                    })
                    .catch(function(err){ 
                        var msg = "Couldn't submit comment, try again";

                        if (err.data.message || err.message ){
                            msg = err.data.message;
                        }

                        ngToast.create({
                                content: msg
                        }); 
                    })
                    .then(function(){
                        afterSubmit();
                    });
                };

                $scope.hide = function(){
                    $scope.toggle = true;
                };                

                beforeSubmit();
            }]
        };
    });