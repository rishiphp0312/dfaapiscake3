angular.module(appConfig.appName)
.run(['$rootScope', '$state', '$urlRouter', 'AUTH_EVENTS', 'authService', function ($rootScope, $state, $urlRouter, AUTH_EVENTS, authService) {
    $rootScope.$on('$stateChangeStart', function (event, next, params) {
        authService.isAuthenticated().then(function (isAuthenticated) {
            //$urlRouter.sync();
            if (next.data != undefined && next.data.authenticationRequired != undefined) {
                if (next.data.authenticationRequired) {
                    if (isAuthenticated) {
                        var authorizedRoles = next.data.authorizedRoles;
                        authService.isAuthorized(authorizedRoles, params.dbId)
                        .then(function (isAuth) {
                            if (!isAuth) {
                                event.preventDefault();
                                $rootScope.$broadcast(AUTH_EVENTS.notAuthorized);
                                $state.go('DataAdmin.notAuthorized');
                            }
                        })
                    } else {
                        event.preventDefault();
                        $rootScope.$broadcast(AUTH_EVENTS.notAuthenticated);
                        location.href = _WEBSITE_URL + '#/?loggedOut=true';
                    }
                }
            } else {
                event.preventDefault();
            }
        });

    });

} ])

.config(['$stateProvider', '$urlRouterProvider', '$httpProvider', 'USER_ROLES', function ($stateProvider, $urlRouterProvider, $httpProvider, USER_ROLES) {

    $httpProvider.interceptors.push('httpInterceptor');

    $urlRouterProvider.otherwise('/');

    $stateProvider
        .state('DataAdmin', {
            url: '/',
            views: {
                'header': {
                    templateUrl: 'js/app/components/core/views/header.html',
                    controller: 'loginController'
                },
                'content': {
                    templateUrl: 'js/app/components/core/views/home.html'
                },
                'footer': {
                    templateUrl: 'js/app/components/core/views/footer.html'
                }
            },
            data: {
                authenticationRequired: false
            }
        })

        .state('DataAdmin.notAuthorized', {
            url: 'NotAuthorized',
            views: {
                'content@': {
                    templateUrl: 'js/app/components/core/views/notAuthorized.html'
                }
            }
        })

        .state('DataAdmin.database', {
            url: 'Database',
            views: {
                'content@': {
                    templateUrl: 'js/app/components/database/views/database.html',
                    controller: 'databaseController'
                }
            },
            data: {
                authorizedRoles: [USER_ROLES.all],
                authenticationRequired: true
            }
        })

        .state('DataAdmin.newDatabaseConnection', {
            url: 'newDatabaseConnection',
            views: {
                'content@': {
                    templateUrl: 'js/app/components/database/views/newDatabaseConnection.html',
                    controller: 'newDatabaseConnectionController'
                }
            },
            data: {
                authenticationRequired: true,
                authorizedRoles: [USER_ROLES.superAdmin]
            }
        })

        .state('DataAdmin.confirmPassword', {
            url: 'UserActivation/:key',
            views: {
                'content@': {
                    templateUrl: 'js/app/components/userManagement/views/confirmPassword.html',
                    controller: 'confirmPasswordController'
                }
            },
            data: {
                authenticationRequired: false
            }
        })

        .state('DataAdmin.databaseManagement', {
            url: 'DatabaseManagement/:dbId',
            views: {
                'content@': {
                    templateUrl: 'js/app/components/databaseManagement/views/databaseManagement.html',
                    controller: 'databaseManagementController'
                }
            },
            data: {
                authorizedRoles: [USER_ROLES.all],
                authenticationRequired: true
            }
        })

        .state('DataAdmin.databaseManagement.userManagement', {
            url: '/UserManagement',
            views: {
                'ManagementView@DataAdmin.databaseManagement': {
                    templateUrl: 'js/app/components/userManagement/views/userManagement.html',
                    controller: 'userManagementController'
                }
            },
            data: {
                authorizedRoles: [USER_ROLES.admin, USER_ROLES.superAdmin],
                authenticationRequired: true
            }
        })

        .state('DataAdmin.databaseManagement.modifyUser', {
            url: '/modifyUser/:userId',
            views: {
                'ManagementView@DataAdmin.databaseManagement': {
                    templateUrl: 'js/app/components/userManagement/views/addModifyUser.html',
                    controller: 'addModifyUserController'
                }
            },
            data: {
                authorizedRoles: [USER_ROLES.admin, USER_ROLES.superAdmin],
                authenticationRequired: true
            }
        })

        .state('DataAdmin.databaseManagement.addNewUser', {
            url: '/addNewUser',
            views: {
                'ManagementView@DataAdmin.databaseManagement': {
                    templateUrl: 'js/app/components/userManagement/views/addModifyUser.html',
                    controller: 'addModifyUserController'
                }
            },
            data: {
                authorizedRoles: [USER_ROLES.admin, USER_ROLES.superAdmin],
                authenticationRequired: true
            }
        })

        .state('DataAdmin.databaseManagement.iusManagement', {
            url: '/IUSManagement',
            views: {
                'ManagementView@DataAdmin.databaseManagement': {
                    templateUrl: 'js/app/components/iusManagement/views/iusManagement.html',
                    controller: 'iusManagementController'
                }
            },
            data: {
                authorizedRoles: [USER_ROLES.admin, USER_ROLES.superAdmin, USER_ROLES.templateUser],
                authenticationRequired: true
            }
        })

        .state('DataAdmin.databaseManagement.importExportManagement', {
            url: '/importExportManagement',
            views: {
                'ManagementView@DataAdmin.databaseManagement': {
                    templateUrl: 'js/app/components/importExportManagement/views/templateImportExport.html',
                    controller: 'templateImportExportController'
                }
            },
            data: {
                authorizedRoles: [USER_ROLES.admin, USER_ROLES.templateUser],
                authenticationRequired: true
            }
        })
} ])