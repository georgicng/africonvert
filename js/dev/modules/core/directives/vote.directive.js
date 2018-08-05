angular
    .module('com.module.core')
    .directive('vote', function (env) {
        return {
            restrict: 'E',
            scope: {
                resolve: '=',
                close: '&',
                dismiss: '&'
            },
            templateUrl: env.partials + 'vote-modal.html',
            controller: ['$scope', '$element', 'VotingService', function ($scope, $element, VotingService) {

                $scope.vote = function (/* user */) {
                    VotingService.save({
                        post: $scope.resolve.params.post,
                        postName: $scope.resolve.params.postName,
                        contest: $scope.resolve.params.contest,
                        //captcha: user.captcha,
                        //username: user.username
                    })
                    .$promise.then(function (res) {
                        $scope.close({ $value: res });
                    })
                    .catch(function (err) {
                        $scope.close({ $value: err });
                    });
                }

                $scope.cancel = function () {
                    $scope.dismiss({ $value: false });
                };
            }]
        };
    });