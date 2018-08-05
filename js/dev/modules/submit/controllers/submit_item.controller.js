function SubmitItemController($scope, $stateParams, contest, SubmitService, $window, $rootScope, lodash, Store) {
  $scope.entries;
  $scope.getting_entries = false;
  $scope.entries_next_page = null;
  $scope.post = contest;
  $scope.id = $stateParams.id;

  //for testing purposes, remove before live
  $scope.post.acf.wistia_project_id = contest.acf.wistia_project_id ? contest.acf.wistia_project_id : '2743830';

  $scope.getEntries = function () {
    $scope.entries_next_page = false;

    if ($scope.entries != undefined)
      return;

    SubmitService.getEntries($stateParams.id)
      .then(function (data) {
        $scope.entries = data.entries;
        $scope.entries_next_page = data.next_page;
        $scope.$apply();
      })
      .catch(function (error) {
        $scope.entries = null;
        $scope.entries_next_page = false;
        $scope.$apply();
      });
  };


  $scope.moreEntries = function () {
    if ($scope.getting_entries == true)
      return;
    $scope.getting_entries = true;

    SubmitService.getEntries($stateParams.id, $scope.entries_next_page)
      .then(function (response) {
        $scope.entries = $scope.entries.concat(response.entries);
        $scope.entries_next_page = response.next_page;
        $scope.getting_entries = false;
      })
      .catch(function (err) {
        if (err == "Last")
          //$scope.entries_next_page = false;
          $scope.getting_entries = false;
      });
  };

  var getEmbedURL = function (key) {
    return 'http://fast.wistia.com/embed/iframe/' + key;
  };

  $scope.postEntry = function (model, response) {
    $scope.success = true;
    $window.location.reload();
  }

  $scope.fileUploadError = function (response) {
    $scope.error = true;
    if (response.status >= 500)
      $scope.errorMsg = "Sorry, you cannot upload files at this time";
    else
      $scope.errorMsg = "Error, cannot upload file";
    //response.status + ': ' + response.data.error;
  }

  $scope.getStatus = function (txt) {
    $output = ''
    switch (txt) {
      case 'publish':
        $output = 'Approved';
        break;
      case 'draft':
        $output = 'Unapproved';
        break;
      case 'pending':
        $output = 'Processing';
        break;
    }
    return $output;
  };

}

angular
  .module('com.module.submit')
  .controller('SubmitItemController', SubmitItemController);