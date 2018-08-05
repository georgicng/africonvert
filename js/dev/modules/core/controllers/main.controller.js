function MainController($state, $rootScope, $scope, $uibModal, $log, $window, headermenu, footermenu, Auth, env, MemberService, ngToast) {

  $scope.animationsEnabled = false;
  $scope.forms = {};
  $scope.uploadData = {};
  $scope.env = env;

  x = $rootScope;
  $rootScope.isNavCollapsed = true;

  $scope.toggle = function () {
    $rootScope.isNavCollapsed = !$rootScope.isNavCollapsed;
  };

  $scope.search = function (query) {
    var term = query.replace(' ', '+');
    $state.go('app.search', {
      term: term
    });
  };

  $scope.login = function () {

    var modalInstance = $uibModal.open({
      animation: $scope.animationsEnabled,
      component: 'login'
    });

    modalInstance.result
      .then(function (response) {
        if (response) {
          ngToast.create({
            content: "You are now logged in"
          });
          $window.location.reload();
          //$state.go($state.current, {}, {reload: true}); 
          /*$state.transitionTo($state.current, $stateParams, {
					reload: true,
					inherit: true,
					notify: true
				});*/
        } else {}
      }, function (response) {
        switch (response) {
          case false:
            break;
          case 'register':
            $scope.register();
            break;
          case 'reset':
            $scope.reset();
            break;
        }

      });

  };

  $scope.logout = function () {
    Auth.logout();
    //$state.go('app.home');
    ngToast.create({
      verticalPosition: 'bottom',
      content: "You're successfully logged out"
    });
    //$window.location.reload();
    $stateParams = "";
    $state.transitionTo('app.home', $stateParams, {
      reload: true,
      inherit: false,
      notify: true
    });
  };

  $scope.register = function () {
    var modalInstance = $uibModal.open({
      animation: $scope.animationsEnabled,
      component: 'register',
    });

    modalInstance.result
      .then(function (response) {
        if (response) {
          ngToast.create({
            content: "Registration successful"
          });
        } else {
          $uibModal.open({
            ariaLabelledBy: 'modal-title-bottom',
            ariaDescribedBy: 'modal-body-bottom',
            template: "<strong>Error encountered</strong>",
            size: 'sm'
          });
        }
      }, function () {});
  };

  $scope.reset = function (user) {
    var modalInstance = $uibModal.open({
      animation: $scope.animationsEnabled,
      component: 'reset',
    });

    modalInstance.result
      .then(function (response) {
        ngToast.create({
          verticalPosition: 'bottom',
          content: "Password has been reset successfully"
        });
      }, function () {
        ngToast.create({
          verticalPosition: 'bottom',
          content: "Couldn't complete request"
        });
      });
  };

  $scope.verify = function () {
    MemberService.verify(Auth.getUserId())
      .then(function (response) {
        if (response.data) {
          ngToast.create({
            verticalPosition: 'bottom',
            content: 'Verification email has been sent'
          });
        } else {
          ngToast.create({
            verticalPosition: 'bottom',
            content: "Couldn't complete your request, please try again"
          });
        }

      })
      .catch(function (err) {
        ngToast.create({
          verticalPosition: 'bottom',
          content: "That's odd, we couldn't find your account"
        });
      });

  };


  $scope.headermenu = headermenu;
  $scope.footermenu = footermenu;

}

angular
  .module('com.module.core')
  .controller('MainController', MainController);