angular
    .module('com.module.core')
    .directive('searchForm', function ($timeout, env) {
        return {
            restrict: 'E',
            templateUrl: env.partials + 'search-form.html',
            replace: true,
            scope: {
                search: "&"
            },
            link: function (scope, element) {
                angular.element("#s_btn_close").on('click', function(e){
                    angular.element(".input_s_div").css({display:"none"})
                    angular.element(".s_btn_div").css({display:"block"}) 
                });
                element.find('.input_s').on('keyup',function(e){
                    if(e.keyCode === 13) {
                        var query = scope.searchTerm;
                        scope.searchTerm = "";
                        element.find("#s_btn_close").trigger('click');
                        scope.search({term: query});
                    }
                });
               
            }
        };
    });

