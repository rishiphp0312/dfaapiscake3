angular.module(appConfig.appName)
.service('session', function () {

    this.create = function (sessionId, userId, userRole) {
        this.id = sessionId;
        this.userId = userId;
        this.userRole = userRole;
        this.dbRole = '';
    };

    this.destroy = function () {
        this.id = null;
        this.userId = null;
        this.userRole = null;
        this.dbRole = null;
    };

    this.updateDbRole = function (dbRole) {
        this.dbRole = dbRole;
    }

})
.factory('authService', ['$http', '$q', 'session', function ($http, $q, session) {

    var authService = {};

    authService.login = function (credentials) {

        var deferred = $q.defer();

        var req = {
            method: 'POST',
            url: 'users/login',
            data: $.param(credentials),
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }

        $http(req).success(function (res) {
            if (res.isAuthenticated) {
                var data = {
                    id: "1552215589",
                    user: {
                        id: '123',
                        name: 'Navdeep Nurpuri',
                        role: ['Admin']
                    }
                }

                session.create(data.id, data.user.id, data.user.role);

                deferred.resolve(data.user);
            } else {
                deferred.resolve('');
            }

        });

        return deferred.promise;

    }

    authService.isAuthenticated = function () {
        return !!session.userId;
    };

    authService.isAuthorized = function (authorizedRoles) {

        var isAuthorized = false;

        if (!angular.isArray(authorizedRoles)) {
            authorizedRoles = [authorizedRoles];
        }


        angular.forEach(session.userRole, function (value) {
            if (authorizedRoles != undefined && (authorizedRoles.indexOf(value) >= 0 || authorizedRoles.indexOf('*'))) {
                isAuthorized = true;
            }
        })

        return (authService.isAuthenticated() && isAuthorized);
    };

    authService.logout = function () {

        var deferred = $q.defer();

        var req = {
            method: 'POST',
            url: 'users/logout',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }

        $http(req).success(function (res) {
            deferred.resolve(res);
            session.destroy();
        });

        return deferred.promise;

    }

    return authService;

} ]);