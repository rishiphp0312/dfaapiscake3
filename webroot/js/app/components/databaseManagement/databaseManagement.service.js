angular.module('DataAdmin.databaseManagement')
.factory('databaseManagementService', ['$http', '$q', 'session', 'commonService', 'SERVICE_CALL', function ($http, $q, session, commonService, SERVICE_CALL) {
    var databaseManagementService = {};

    databaseManagementService.getDatabaseDetails = function (databaseId) {

        var deferred = $q.defer();

        $http(commonService.createHttpRequestObject(SERVICE_CALL.database.getDatabaseDetails, { dbId: databaseId }))
        .success(function (res) {
            if (res.success) {
                deferred.resolve(res.data.dbDetail);
            } else {
                defferred.reject(res.err);
            }
        });

        return deferred.promise;

    }

    return databaseManagementService;

} ])