angular
    .module('com.module.core')
    .config(function($stateProvider, $urlRouterProvider, $locationProvider) {
           
        $locationProvider.html5Mode(true);
        
        $stateProvider
             .state('app', {
                url: "",              
                templateUrl: local_env.partials + 'layout.html',
                controller: 'MainController',
                resolve: {
                    headermenu : function(MenuService){
                       return MenuService.query({'menu':'header-menu'}).$promise;
                    },
                    footermenu : function(MenuService){
                       return MenuService.query({'menu':'footer-menu'}).$promise;
                    }
                }
            })
            .state({
                name: 'app.error',
                url: '/error',
                templateUrl: local_env.partials + '404.html',
                data: {
                    pageTitle: "404"
                }
            })
            .state("app.search",{
                url: '/search/:term', 			
                templateUrl: local_env.partials + 'search.html',
                controller: 'SearchController',
                resolve: {
                    matches: function(SearchService, $stateParams){
                        return SearchService.get({term:$stateParams.term}).$promise;
                    }
                },
                data: {
                    pageTitle: "Search Result Page"
                }                    
            });

        $urlRouterProvider.otherwise("/home");
    });