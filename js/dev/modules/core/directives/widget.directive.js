angular
    .module('com.module.core')
    .directive('widget', function(env) {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: env.partials + 'widget.html',
            scope :{
                name: '=',
                instance: '=',
                args: '=',
            },
            controller: ['$scope', '$element', '$http', "$sce", function( $scope, $element, $http, $sce ){
                $scope.html = "";
                $scope.loading = true;
                 $http.post(env.api_url + '/widgets/' + $scope.name, { instance:$scope.instance}).then(function (response) {
                      
                    if (response) {
                            $scope.html = response.data.content;
                            $scope.loading = false;
                        }
                    }).catch(function (response) {
                        $scope.html = "Nothing to display";
                        $scope.loading = false;
                    });
            }]
        };
    });