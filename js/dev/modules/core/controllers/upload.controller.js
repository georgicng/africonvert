function UploadController($scope, SubmissionService, Upload, $timeout) {
    $scope.doUpload = function() {
      if ($scope.forms.upload.file.$valid && $scope.uploadData .file) {
        $scope.upload($scope.uploadData.file);
      }
    };
	$scope.upload = function (file) {
        Upload.upload({
            url: 'https://upload.wistia.com',
            data: {
				file: file,
				api_password:'ad66504c73b0f8abeb01add55c3b0c4e22abc6025634d6462d3f4f31f7fb4944',
				project_id: '2743830',
				name: $scope.uploadData.name,
				description: $scope.uploadData.description
			}
        }).then(function (response) {
			file.result = response.data;
			$timeout(function () {
				SubmissionService.save({
					title: $scope.uploadData.name,
					content: $scope.uploadData.description, 
					fields: {
						wistia_id : response.data.id,
						wistia_hash : response.data.hashed_id,
						type: response.data.type,
						thumbnail: response.data.thumbnail.url
					}
				}, function(response){
				}, function(response){
				});
			});
			//SharedState.turnOff('upload');
        }, function (response) {
			if (response.status > 0)
        	$scope.uploadData.errorMsg = response.status + ': ' + response.data;
			//SharedState.turnOff('upload');
        }, function (evt) {
            //var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
			file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
 		    //console.log('progress: ' + progressPercentage + '% ' + evt.config.data.file.name);
        });
    };
}

angular
    .module('com.module.core')
    .controller('UploadController', UploadController);