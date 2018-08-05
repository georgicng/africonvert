function ConfirmController(response, $scope, $rootScope, $localStorage, Auth) {
    if (response.data){
        $scope.confirmed = true;
        Auth.logout();
    } else {
        $scope.confirmed = false;
    }
        
}

angular
    .module('com.module.members')
    .controller('ConfirmController', ConfirmController);