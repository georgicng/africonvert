function ProfileController($scope, $state, $stateParams, MemberService, Auth, $rootScope, $localStorage, ngToast, Upload, $timeout, env) {
  $scope.user = $rootScope.user ? $rootScope.user.user_data : undefined;
  $scope.account = $scope.user;
  $scope.pic = {};

  $scope.changePassword = function (account) {
    var user = {
      password: account.newPassword,
      id: $scope.account.id
    };
    MemberService.changePassword(user)
      .then(function (success) {
        Auth.logout();
        $scope.auth = undefined;
        ngToast.create({
          verticalPosition: 'bottom',
          content: 'Password changed'
        });
        $state.go('app.home', {}, { reload: true });
      })
      .catch(function (failure) {
        ngToast.create({
          verticalPosition: 'bottom',
          content: "Couldn't update password"
        });
      });
  };

  $scope.updateProfile = function (account) {

    delete $scope.account.joined;
    delete $scope.account['last-login'];
    delete $scope.account['last-active'];

    MemberService.changeInfo(account)
      .then(function (data) {
        $rootScope.user.user_data = data.profile;
        $localStorage.user.user_data = data.profile;
        $scope.user = data.profile;
        $scope.account = data.profile;
        $scope.$apply();
        ngToast.create({
          verticalPosition: 'bottom',
          content: "Profile updated"
        });

      })
      .catch(function (err) {
        ngToast.create({
          verticalPosition: 'bottom',
          content: "Couldn't update profie"
        });
      });
  };


  $scope.uploadImage = function (file, errFiles, type) {
    $scope.errFile = errFiles && errFiles[0];
    if (file) {
      Upload.base64DataUrl(file)
        .then(
          function (url) {
            file.upload = Upload.http({
              url: env.api_url + '/xprofile',
              data: {
                "image": url,
                "type": type
              }
            });

            file.upload.then(
              function (response) {
                var response = response.data;
                if (response.status == "success") {
                  $scope.user = response.data;
                  $localStorage.user.user_data = response.data;
                  $rootScope.user.user_data = response.data;
                  ngToast.create({
                    verticalPosition: 'bottom',
                    content: 'Avatar changed successfully'
                  });
                } else {
                  ngToast.create({
                    verticalPosition: 'bottom',
                    content: response.message
                  });
                }
              },
              function (response) {
                ngToast.create({
                  verticalPosition: 'bottom',
                  content: 'Something went wrong'
                });
              },
              function (evt) {
                file.progress = Math.min(100, parseInt(100.0 *
                  evt.loaded / evt.total));
              }
            );
          }
        );

    }
  };

  $scope.removeCover = function () {
    MemberService.removeCover()
      .then(function (response) {
        if (response.status == "success") {
          $scope.user = response.data;
          $localStorage.user.user_data = response.data;
          $rootScope.user.user_data = response.data;
          ngToast.create({
            verticalPosition: 'bottom',
            content: 'Avatar removed successfully'
          });
        } else {
          ngToast.create({
            verticalPosition: 'bottom',
            content: "Couldn't remove avatar"
          });
        }

      })
      .catch(function (err) {
        ngToast.create({
          verticalPosition: 'bottom',
          content: 'Something went wrong'
        });
      });
  };

}

angular
  .module('com.module.members')
  .controller('ProfileController', ProfileController);
