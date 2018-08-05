angular
  .module('com.module.core')
  .directive('upload', function (env) {
    return {
      restrict: 'E',
      scope: {
        validation: '=',
        project: '=',
        category: '=',
        success: '&',
        error: '&'
      },
      templateUrl: local_env.partials + 'upload.html',
      controller: ['$scope', 'Upload', function ($scope, Upload) {
        $scope.doUpload = function () {
          if ($scope.forms.upload.file.$valid && $scope.uploadData.file) {
            upload($scope.uploadData);
          }
        };

        var upload = function (model) {
          var params = {
            url: env.api_url + '/upload',
            data: {
              media: model.file,
              thumbnail: model.thumbnail,
              name: model.name,
              description: model.description,
              category: $scope.category

            }
          };
          var handle = Upload.upload(params);
          handle.then(function (response) {
            $scope.uploaded = true;
            $scope.type = response.data.type;
            $scope.file = model.file;
            $scope.success()($scope.uploadData, response.data);
          }, function (response) {
            $scope.error()(response);
          }, function (evt) {
            model.file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
          });
        };
      }]
    };
  });