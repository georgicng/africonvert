angular
    .module('com.module.core')
    .directive('notification', function (env) {
        return {
            restrict: 'E',
            replace: true,
            scope: {
                user: '='
            },
            templateUrl: env.partials + 'notifications.html',
            controller: ['$scope', '$element', 'MemberService', '$log', function ($scope, $element, MemberService, $log) {
                $log.log('User data: ', $scope.user);
                MemberService.getUserNotifications($scope.user)
                .then(function(data, header){
                    $scope.notifications = data;
                    $scope.count = header('latest');
                    $log.log('Count: ', header('latest'));
                })
                .catch(function(err){
                });

                $scope.seen = function () {
                    MemberService.notificationUpdateSeen($scope.user)
                    .then(function(data){

                    })
                    .catch(function(notif){

                    });
                };

                $scope.read = function (id) {
                   MemberService.notificationRead($scope.user, id)
                   .then(function(data){

                    })
                    .catch(function(notif){

                    });
                };

                $scope.status = {
                    isopen: false
                };

                $scope.toggled = function(open) {
                    $log.log('Dropdown is now: ', open);
                };

            }]
        };
    });