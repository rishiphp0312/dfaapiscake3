angular.module('DataAdmin.databaseManagement')
.factory('databaseManagementService', ['$http', '$q', 'session', function ($http, $q, session) {
    var databaseManagementService = {};

    databaseManagementService.getDatabaseDetails = function (databaseId) {

        var deferred = $q.defer();

        var data = {
            isAuthenticated: true,
            isAuthorized: true,
            databaseDetails: {
                id: 1,
                name: 'Assam Tea Garden'
            },
            dbRole: ['DataUser'],
            role: ['Super Admin', 'Admin']
        }

        session.updateDbRole(data.dbRole);

        deferred.resolve(data.databaseDetails);

        return deferred.promise;

    }

    return databaseManagementService;
} ])