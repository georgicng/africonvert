angular
    .module('com.module.core')
    .directive('register', function () {
        return {
            restrict: 'E',
            scope: {
                resolve: '=',
                close: '&',
                dismiss: '&'
            },            			
            templateUrl: local_env.partials + 'register.html',
            controller: ['$scope', '$element', 'Auth', function ($scope, $element, Auth) {
                $scope.register = function (record) {
                    $scope.error = false;
                    $scope.errormsg = "";
                    Auth.register(record, function(data){ 
                            $scope.close({$value: true});
                        }, 
                        function(err){
                            $scope.error = true;
                            $scope.errormsg = err.message;
                        }
                    ); 
                };
                $scope.cancel = function () {
                    $scope.dismiss({ $value: false });
                };
            }]
        };
    });