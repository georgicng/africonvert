angular
    .module('com.module.members')
    .config(function($stateProvider) {       
        $stateProvider
            .state('app.member', {
                url: '/members/:id',
                templateUrl: local_env.partials + 'member.html',
                controller: 'MemberController',
                resolve: {
                    member: function($stateParams, MemberService) {
                        return MemberService.getUser($stateParams.id);
                    }
                },
                data: {
                    pageTitle: "Member Profile"
                }	
            })
            .state('app.profile', {
                url: '/members/me/profile',
                templateUrl: local_env.partials + 'profile.html',
                controller: 'ProfileController',
                data: {
                    pageTitle: "Your Profile"
                }
            })            
            .state("app.confirm",{
                url: '/confirm/:token', 			
                templateUrl: local_env.partials + 'confirm.html',
                controller: 'ConfirmController',
                resolve: {
                    response : function(MemberService, $stateParams){
                        return MemberService.confirm($stateParams.token);
                    }
                },
                data: {
                    pageTitle: "User Profile"
                }	                    
            });
    });