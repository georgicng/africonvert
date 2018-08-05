angular
    .module('com.module.core')
    .directive('checkRequired', function () {
        return {
            require: 'ngModel',
            restrict: 'A',
            link: function (scope, element, attrs, ngModel) {
                ngModel.$validators.checkRequired = function (modelValue, viewValue) {
                    var value = modelValue || viewValue;
                    var match = scope.$eval(attrs.ngTrueValue) || true;
                    return value && match === value;
                };
            }
        };
    });
