angular
    .module('com.module.core')
    .directive('login', function (env) {
        return {
            restrict: 'E',
            scope: {
                resolve: '=',
                close: '&',
                dismiss: '&'
            },
            templateUrl: env.partials + 'login.html',
            controller: ['$scope', '$element', 'Auth', function ($scope, $element, Auth) {
                $scope.login = function (account) {
                    $scope.failed = false;                
                    Auth.login(
                        account, 
                        function(data){ 
                            $scope.close({$value: true});
                        }, 
                        function(err){
                            setError("Login failed, Please try again");
                        }
                    ); 
                };

                $scope.cancel = function () {
                    $scope.dismiss({$value: false});
                };

                $scope.reset = function () {
                    $scope.dismiss({$value: 'reset'});
                };

                $scope.register = function () {
                    $scope.dismiss({$value: 'register'});
                };

                var setError = function (message) {
                    $scope.failed = true;
                    $scope.errorMsg = message;
                };
            }]
        };
    });