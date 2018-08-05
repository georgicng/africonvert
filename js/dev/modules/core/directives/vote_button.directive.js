angular
    .module('com.module.core')
    .directive('voteButton', function () {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: local_env.partials + 'vote-button.html',
            scope: {
                post: '=',
                contest: '=',
                canVote: '=',
                title: '=',
                success: '&',
                error: '&',
            },
            controller: ['$scope', '$element', 'ngToast', '$uibModal', '$log', function ($scope, $element, ngToast, $uibModal, $log) {
                
                $scope.voted = null;

                $scope.vote = function () {
                    var modalInstance = $uibModal.open({
                        component: 'vote',
                        resolve: {
                            params: function () {
                                return {
                                    post: $scope.post,
                                    contest: $scope.contest,
                                    postName: $scope.title
                                }
                            }
                        }
                    });

                    modalInstance.result.then(function (data) {
                        
                        if(data.status == 'success') {                            
                            $scope.voted = true;
                            $scope.success()(data.data);
                        }

                        if(data.status > 400) {
                            $scope.error()(data.data); 
                        }
                        
                    }, function () {
                        $log.info('modal-component dismissed at: ' + new Date());
                    });
                };


            }]
        };
    });