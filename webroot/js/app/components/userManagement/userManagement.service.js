angular.module('DataAdmin.userManagement')
.factory('userManagementService', ['$http', '$q', function ($http, $q) {

    var userManagementService = {};

    userManagementService.getUsersList = function (databaseId) {

        var deferred = $q.defer();

        var data = {
            isAuthenticated: true,
            isAuthorized: true,
            data: [{
                id: 1,
                name: 'test1',
                email: 'test1@test.com',
                roles: ['Admin'],
                access: { area: 0, indicator: 0 },
                lastLoggedIn: '10-01-2012'
            },
            {
                id: 2,
                name: 'test2',
                email: 'test2@test.com',
                roles: ['Admin'],
                access: { area: 0, indicator: 0 },
                lastLoggedIn: '10-01-2012'
            }, {
                id: 3,
                name: 'test3',
                email: 'test3@test.com',
                roles: ['DataUser'],
                access: { area: 0, indicator: 0 },
                lastLoggedIn: '10-01-2012'
            }, {
                id: 4,
                name: 'test4',
                email: 'test4@test.com',
                roles: ['DataUser','TemplateUser'],
                access: { area: 0, indicator: 0 },
                lastLoggedIn: '10-01-2012'
            }, {
                id: 5,
                name: 'test5',
                email: 'test5@test.com',
                roles: ['DataUser','TemplateUser'],
                access: { area: 0, indicator: 0 },
                lastLoggedIn: '10-01-2012'
            }, {
                id: 6,
                name: 'test6',
                email: 'test6@test.com',
                roles: ['TemplateUser'],
                access: { area: 0, indicator: 0 },
                lastLoggedIn: '10-01-2012'
            }, {
                id: 7,
                name: 'test7',
                email: 'test7@test.com',
                roles: ['Admin'],
                access: { area: 0, indicator: 0 },
                lastLoggedIn: '10-01-2012'
            }, {
                id: 8,
                name: 'test8',
                email: 'test8@test.com',
                roles: ['DataUser','TemplateUser'],
                access: { area: 0, indicator: 0 },
                lastLoggedIn: '10-01-2012'
            }],
            dbRole: ['Admin'],
            role: ['Super Admin', 'Admin']
        }

        deferred.resolve(data);

        return deferred.promise;

    }

    return userManagementService;

} ])