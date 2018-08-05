function UploadService($resource) {
    var EntryService = {};

    var API = $resource(local_env.api_url + '/submissions/:id',
        {
            id: '@id'
        },
        {
            save: { method: 'POST' }

        }
    );

    EntryService.saveEntry = function (post) {
        return new Promise(function (resolve, reject) {
            API.save(post, function (data) {
                if (!data) {
                    reject("No response");
                } else {
                    resolve(data);
                }
            });
        });
    }

    return EntryService;
}

angular
    .module('com.module.core')
    .factory('UploadService', UploadService);