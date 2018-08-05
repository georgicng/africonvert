function VotingService($resource){    
		return $resource(local_env.api_url+'/vote/',
        {},
        {
            save: { method: 'POST' }

        });
}

angular
    .module('com.module.core')
    .factory('VotingService', VotingService);