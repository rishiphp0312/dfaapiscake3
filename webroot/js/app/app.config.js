angular.module(appConfig.appName)
.config(['$stateProvider', '$urlRouterProvider', 'USER_ROLES', function ($stateProvider, $urlRouterProvider, USER_ROLES) {

    $urlRouterProvider.otherwise('/');

    $stateProvider
        .state('DataAdmin', {
            url: '/',
            views: {
                'header': {
                    templateUrl: 'js/app/components/home/views/header.html',
                    controller: 'loginController'
                },
                'content': {
                    templateUrl: 'js/app/components/home/views/home.html'
                },
                'footer': {
                    templateUrl: 'js/app/components/home/views/footer.html'
                }
            }
        })

        .state('DataAdmin.database', {
            url: 'database',
            views: {
                'content@': {
                    templateUrl: 'js/app/components/database/views/database.html',
                    controller: 'databaseController'
                }
            },
            data: {
                authorizedRoles: [USER_ROLES.admin, USER_ROLES.superAdmin, USER_ROLES.dataUser, USER_ROLES.templateUser]
            }
        })

        .state('DataAdmin.newDatabaseConnection', {
            url: 'newDatabaseConnection',
            views: {
                'content@': {
                    templateUrl: 'js/app/components/database/views/newDatabaseConnection.html',
                    controller: 'newDatabaseConnectionController'
                }
            }
        })

        .state('DataAdmin.databaseManagement', {
            url: 'DatabaseManagement/:id',
            views: {
                'content@': {
                    templateUrl: 'js/app/components/databaseManagement/views/databaseManagement.html',
                    controller: 'databaseManagementController'
                }
            }
        })

        .state('DataAdmin.databaseManagement.userManagement', {
            url: '/UserManagement',
            views: {
                'ManagementView@DataAdmin.databaseManagement': {
                    templateUrl: 'js/app/components/userManagement/views/userManagement.html',
                    controller: 'userManagementController'
                }
            }
        })

        .state('DataAdmin.databaseManagement.addNewUser', {
            url: '/addNewUser',
            views: {
                'ManagementView@DataAdmin.databaseManagement': {
                    templateUrl: 'js/app/components/userManagement/views/addNewUser.html'
                }
            }
        })


        .state('DataAdmin.databaseManagement.iusManagement', {
            url: '/IUSManagement',
            views: {
                'ManagementView@DataAdmin.databaseManagement': {
                    templateUrl: 'js/app/components/iusManagement/views/iusManagement.html'
                }
            }
        })

        .state('DataAdmin.databaseManagement.importExportManagement', {
            url: '/importExportManagement',
            views: {
                'ManagementView@DataAdmin.databaseManagement': {
                    templateUrl: 'js/app/components/importExportManagement/views/templateImportExport.html'
                }
            }
        })
} ])