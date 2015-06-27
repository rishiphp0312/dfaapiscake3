// Init the application configuration module for AngularJS application
var appConfig = (function () {
    // Init module configuration options
    var appName = 'DataAdmin';

    var appDependencies = [
        'ui.router',
        'ngDialog',
        'DataAdmin.login',
        'DataAdmin.database',
        'DataAdmin.databaseManagement',
        'DataAdmin.userManagement'
    ];

    // Add a new vertical module
    var registerModule = function (moduleName) {
        // Create angular module
        angular.module(moduleName, []);

        // Add the module to the AngularJS configuration file
        angular.module(appName).requires.push(moduleName);
    };

    return {
        appName: appName,
        appDependencies: appDependencies,
        registerModule: registerModule
    };
})();