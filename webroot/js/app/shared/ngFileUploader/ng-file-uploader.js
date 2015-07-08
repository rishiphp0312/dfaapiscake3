angular.module('ngFileUploader', [])
.factory('ngFileUploaderService', ['Upload', '$q', '$http', function (Upload, $q, $http) {

    var ngFileUploaderService = {};

    ngFileUploaderService.uploadFile = function (file, fileData, progressCallBack) {

        var deffered = $q.defer();

        Upload.upload({
            url: fileData.url,
            file: file,
            fields: fileData.fields,
            sendFieldsAs: fileData.sendFieldAs 
        }).progress(function (evt) {
            var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
            progressCallBack(progressPercentage);
        }).success(function (res) {
            if (res.success) {
                deffered.resolve(res.success);
            } else {
                deffered.reject(res.err);
            }
        })

        return deffered.promise;

    }

    return ngFileUploaderService;


} ])
.directive('ngFileUploader', function () {
    return {
        restrict: 'E',
        scope: {
            acceptExt: '=',
            selectFile: '=',
            fileData: '=',
            onFileSuccess: '=',
            onFileFail: '='
        },
        controller: ['$scope', 'ngFileUploaderService', function ($scope, ngFileUploaderService) {
            $scope.file = '';
            $scope.progressPercent = 0;
            $scope.uploadFile = function () {
                ngFileUploaderService
                .uploadFile($scope.file[0], $scope.fileData, function (progressPercent) {
                    $scope.progressPercent = progressPercent;
                })
                .then(function (success) {
                    $scope.onFileSuccess(success, $scope.fileData);
                }, function (err) {
                    $scope.onFileFail(err, $scope.fileData);
                })
            }
        } ],
        template: ('<div class="upload">' +

                    '<div  class="upload-box" ngf-select="selectFile" ngf-accept="acceptExt" ng-model="file">' + // 

                         '<span class="inactive" ng-show="!file[0]">' +
                                'Upload file' +
                        '</span>' +

                         '<span class="loading" ng-progress-bar progress="progressPercent" ng-repeat="file in file">' +
                                '{{file.name}}' +
                         '</span>' +

                    '</div>' +

                    '<a class="btn btn-grey btn-sm" ng-click="file[0] && uploadFile()"><i class="fa fa-upload"></i> Upload </a>' +

                  '</div>')
    }

})