angular
    .module('com.module.core')
    .directive('sidebar', function(env) {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: env.partials + 'sidebar.html',
            scope :{
                id: '=',
            },
            controller: ['$scope', '$element', '$http', "$sce", function( $scope, $element, $http, $sce ){
                $scope.html = "";
                $scope.loading = true;
                 $http.get(env.widgets_url + '/' + $scope.id).then(function (response) {
                        if (response.data) {
                            $scope.html = response.data.rendered;
                            $scope.loading = false;
                        }
                    }).catch(function (response) {
                        $scope.html = "Nothing to display";
                        $scope.loading = false;
                    });
            }]
        };
    });