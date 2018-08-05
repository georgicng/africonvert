function LikeService($resource, env){
    return $resource(env.api_url+'/likes/:id',{
			id:'@id'
		},{			
			'save':{
				method:'POST',
			}
		});
}

angular
    .module('com.module.core')
    .factory('LikeService', LikeService);