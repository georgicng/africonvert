function SearchService($resource, env){
		return  $resource(env.api_url+'/search/:term/:page',{term: '@term', page: '@page'});
}

angular
    .module('com.module.core')
    .factory('SearchService', SearchService);