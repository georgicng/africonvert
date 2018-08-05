angular
    .module('com.module.core')
    .directive('validPasswordC', function () {
        return {
            require: "ngModel",
            link: function (scope, elm, attrs, ctrl) {
                ctrl.$parsers.unshift(function (viewValue, $scope) {
                    var noMatch = viewValue != scope.registerForm.password.$viewValue
                    ctrl.$setValidity('noMatch', !noMatch)
                });
            }
        }
    });
