angular
    .module('com.module.watch')
    .config(function($stateProvider) {       
        $stateProvider
        .state("app.watchlist",{
            url: '/watch?contest&order&orderby&page',
            params: {
                page: "1",
            }, 
            templateUrl: local_env.partials + 'entries.html',
            controller: 'WatchListController',
            resolve: {
                entries: function(WatchService, $stateParams) {
                    console.log($stateParams);
                    var param = {};
                    if ($stateParams.contest !== "" && $stateParams.contest !== undefined){
                        param.contest = $stateParams.contest;
                    }
                    
                    if ($stateParams.order !== "" && $stateParams.order !== undefined){
                        param.order = $stateParams.order;
                    }
                    
                    if ($stateParams.orderby !== "" && $stateParams.orderby !== undefined){
                        param.orderby = $stateParams.orderby;
                    }

                    if ($stateParams.page !== "" && $stateParams.page !== undefined){
                        param.page = $stateParams.page;
                    }

                    return WatchService.getEntries(param);
                },
                categories: function(WatchService) {
                    return WatchService.getCategories();
                }
            },
            data: {
                pageTitle: "Watch List"
            }	
        })
        .state('app.watchitem', {
            url: '/watch/:id',
            templateUrl: local_env.partials + 'entry.html',
            controller: 'WatchItemController',
            resolve: {
                entry: function(WatchService, $stateParams) {
                    return WatchService.getEntry($stateParams.id);
                }
            },
            data: {
                pageTitle: "Watch"
            }	
        });
    });