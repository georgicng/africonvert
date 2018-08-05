function WistiaService($resource){
    return $resource('https://upload.wistia.com',{
		},{
			'upload':{
				method:'POST',
				headers: {
				}
			}
		});
}

angular
    .module('com.module.core')
    .factory('WistiaService', WistiaService);