function MenuService($resource, env){
    return $resource(env.menu_base+'/menu-locations/:menu',{
			menu:'@menu'
		});
}

angular
    .module('com.module.core')
    .factory('MenuService', MenuService);