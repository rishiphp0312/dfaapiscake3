angular.module('DataAdmin.importExportManagement')
.controller('templateImportExportController', ['$scope', '$stateParams', 'templateImportService', 'errorService', 'onSuccessDialogService', function ($scope, $stateParams, templateImportService, errorService, onSuccessDialogService) {

    $scope.progressStatus = {
        ius: 0,
        area: 0
    }

    $scope.files = {
        iusFile: '',
        areaFile: ''
    }

    $scope.dbId = $stateParams.dbId;

    $scope.uploadFile = function (uploadType) {

        var file = (uploadType == 'ICIUS' ? $scope.files.iusFile[0] : $scope.files.areaFile[0]);

        //if (uploadType == 'ICIUS')
        //    $scope.progressStatus.ius = 0;

        //if (uploadType == 'AREA')
        //    $scope.progressStatus.area = 0;

        templateImportService.uploadFile($scope.dbId, file, uploadType, function (progressPercent) {
            if (uploadType == 'ICIUS') {
                $scope.progressStatus.ius = { 'background': 'linear-gradient(90deg, #69E089 ' + progressPercent + '%, white 0%)' };

            } else {
                $scope.progressStatus.area = { 'background': 'linear-gradient(90deg, #69E089 ' + progressPercent + '%, white 0%)' };
            }
        }).then(function (res) {
            var msg = (uploadType == 'ICIUS' ? 'IUS and Indicator Classifications have been imported successfully.' : 'Geographic Areas have been imported successfully.');

            onSuccessDialogService.show(msg, function () {
                if (uploadType == 'ICIUS')
                    $scope.progressStatus.ius = 0;

                if (uploadType == 'AREA')
                    $scope.progressStatus.area = 0;
            });

        }, function (err) {

            errorService.show(err);

            if (uploadType == 'ICIUS') {
                $scope.progressStatus.ius = 0;
                $scope.files.iusFile = '';
            }

            if (uploadType == 'AREA') {
                $scope.progressStatus.area = 0;
                $scope.files.areaFile = '';
            }

        });
    }

} ])