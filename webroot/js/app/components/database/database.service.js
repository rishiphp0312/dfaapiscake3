angular.module('DataAdmin.database')
.factory('databaseService', ['$http', '$q', function ($http, $q) {

    var databaseService = {};

    databaseService.getDbTypeList = function () {

        var deferred = $q.defer();

        deferred.resolve([{
            id: 1,
            name: 'MS SQL'
        }, {
            id: 2,
            name: 'My SQL'
        }])

        return deferred.promise;

    }

    return databaseService;

} ])