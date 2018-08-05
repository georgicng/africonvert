angular
    .module('com.module.core')
    .directive('clickAndDisable', function () {
        return {
            scope: {
                clickAndDisable: '&'
            },
            link: function (scope, iElement, iAttrs) {
                iElement.bind('click', function () {
                    iElement.prop('disabled', true);
                    scope.clickAndDisable().finally(function () {
                        iElement.prop('disabled', false);
                    })
                });
            }
        };
    });