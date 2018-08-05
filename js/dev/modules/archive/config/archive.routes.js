angular
    .module('com.module.archive')
    .config(function($stateProvider) {       
        $stateProvider
            .state("app.archive",{
                url: '/archive', 
                templateUrl: local_env.partials + 'archive.html',
                controller: 'ArchiveController',
                resolve: {
                    contests: function(ArchiveService) {
                        console.log('passed');
                        return ArchiveService.getContests({type: 'submit'});
                    }
                },
                params: {
                    page: '1' //default page
                }
            })
            .state('app.single', {
                url: '/archive/:id', 
                templateUrl: local_env.partials + 'single.html',
                controller: 'SingleController',                
                resolve: {
                    contest: function($stateParams, ArchiveService) {
                        console.log($stateParams.id);
                        return ArchiveService.getContest($stateParams.id);
                    }
                }				
            });
    });