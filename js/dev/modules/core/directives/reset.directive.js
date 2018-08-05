angular
    .module('com.module.core')
    .directive('reset', function (env) {
        return {
            restrict: 'E',
            scope: {
                resolve: '=',
                close: '&',
                dismiss: '&'
            },
            templateUrl: env.partials + 'reset.html',
            controller: ['$scope', '$element', '$http', 'Auth', function ($scope, $element, $http, Auth) {
                $scope.reset = function (user) {
                    $scope.failed = false;

                    if (!user.email) {
                        setError("Email not provided");
                        return;
                    }
                    Auth.reset(user, function () {
                        $scope.close({ $value: true });
                    },
                        function (err) {
                            if (err.message) {
                                setError(err.message);
                            } else {
                                setError("Request Failed, please try again");
                            }
                        }
                    );
                };
                $scope.cancel = function () {
                    $scope.dismiss({ $value: false });
                };
                var setError = function (message) {
                    $scope.failed = true;
                    $scope.errorMsg = message;
                };
            }]
        };
    });