angular
    .module('com.module.submit')
    .config(function($stateProvider) {       
        $stateProvider
            .state("app.submitItems",{
                url: '/submit', 
                templateUrl: local_env.partials + 'submit.html',
                controller: 'SubmitListController',
                resolve: {
                    contests: function(SubmitService, $stateParams) {
                        return SubmitService.getContests({type: 'submit', page: $stateParams.page});
                    }
                },
                params: {
                    page: '1' //default page
                },
                data: {
                    pageTitle: "Contests"
                }	
            })
            .state('app.submitItem', {
                url: '/submit/:id',
                templateUrl: local_env.partials + 'submit-detail.html',
                controller: 'SubmitItemController',                
                resolve: {
                    contest: function($stateParams, SubmitService) {
                        return SubmitService.getContest($stateParams.id, 'submit');
                    }
                },
                params: {
                    project: '2743830' //default page
                },
                data: {
                    pageTitle: "Upload Contest"
                }					
            });
    });