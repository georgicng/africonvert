function LoginController ($uibModalInstance) {
  var $ctrl = this;
  
  $ctrl.ok = function () {
    $uibModalInstance.close('done');
  };

  $ctrl.cancel = function () {
    $uibModalInstance.dismiss('cancel');
  };
}

angular
    .module('com.module.core')
    .controller('LoginController', LoginController); 