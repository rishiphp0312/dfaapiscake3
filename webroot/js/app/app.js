angular.module(appConfig.appName, appConfig.appDependencies)
.run(['$rootScope', '$state', 'AUTH_EVENTS', 'authService', function ($rootScope, $state, AUTH_EVENTS, authService) {
    $rootScope.$on('$stateChangeStart', function (event, next) {
        if (next.data != undefined) {
            var authorizedRoles = next.data.authorizedRoles;
            if (!authService.isAuthorized(authorizedRoles)) {
                event.preventDefault();
                if (authService.isAuthenticated()) {
                    $rootScope.$broadcast(AUTH_EVENTS.notAuthorized);
                } else {
                    $rootScope.$broadcast(AUTH_EVENTS.notAuthenticated);
                }
            }
        }
    });
} ]);


angular.element(document).ready(function () {
    angular.bootstrap(document, [appConfig.appName]);
});