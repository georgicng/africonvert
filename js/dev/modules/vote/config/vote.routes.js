angular
    .module('com.module.vote')
    .config(function($stateProvider) {       
        $stateProvider
            .state("app.vote",{
                url: '/vote', 
                templateUrl: local_env.partials + 'vote.html',
                controller: 'VoteController',
                resolve: {
                    contests: function(VoteService) {
                        return VoteService.getContests({type: 'vote'});
                    }
                },
                params: {
                    page: '1' //default page
                },
                data: {
                    pageTitle: "Vote"
                }	
            })
            .state('app.voteItems', {
                url: '/vote/:contest_id',
                templateUrl: local_env.partials + 'vote-entries.html',
                controller: 'VoteEntriesController',
                resolve: {
                    contest: function($stateParams, VoteService) {
                        return VoteService.getContest($stateParams.contest_id);
                    },
                    entries: function($stateParams, VoteService) {
                        return VoteService.getEntries($stateParams.contest_id);
                    }
                },
                params: {
                    page: '1',
                },
                data: {
                    pageTitle: "Vote Entries"
                }		
            })
            .state('app.voteItem', {
                url: '/vote/:contest_id/:entry_id',
                templateUrl: local_env.partials + 'vote-entry.html',
                controller: 'VoteEntryController',
                resolve: {
                    entry: function($stateParams, VoteService) {
                        return VoteService.getEntry($stateParams.entry_id, $stateParams.contest_id);
                    }
                },
                data: {
                    pageTitle: "Vote"
                }		
            });
    });