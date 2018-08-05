function FollowService($resource){    
		return $resource(local_env.api_url+'/follow/',{});
}

angular
    .module('com.module.core')
    .factory('FollowService', FollowService);