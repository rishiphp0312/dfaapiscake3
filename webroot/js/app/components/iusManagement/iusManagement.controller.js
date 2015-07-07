angular.module('DataAdmin.iusManagement')
.controller('iusManagementController', ['$scope', function ($scope) {
    $scope.iusList = [{
        iGid: 'IMR',
        iName: 'Infant Mortality Rate',
        unitList: [{
            uGid: '1',
            uName: 'Deaths Per 1000 live births',
            subgroupList: [{
                iusGid: 'IMR_T',
                sGid: 'Total',
                sName: 'Total'
            }, {
                iusGid: 'IMR_M',
                sGid: 'Male',
                sName: 'Male'
            }, {
                iusGid: 'IMR_F',
                sGid: 'Female',
                sName: 'Female'
            }]
        }, {
            uGid: '2',
            uName: 'Deaths Per 100 live births',
            subgroupList: [{
                iusGid: 'IMR_T',
                sGid: 'Total',
                sName: 'Total'
            }, {
                iusGid: 'IMR_M',
                sGid: 'Male',
                sName: 'Male'
            }, {
                iusGid: 'IMR_F',
                sGid: 'Female',
                sName: 'Female'
            }]
        }]
    }, {
        iGid: 'U5MR',
        iName: 'Under Five Mortality Rate',
        unitList: [{
            uGid: '1',
            uName: 'Deaths Per 1000 live births',
            subgroupList: [{
                iusGid: 'U5MR_T',
                sGid: 'Total',
                sName: 'Total'
            }, {
                iusGid: 'U5MR_M',
                sGid: 'Male',
                sName: 'Male'
            }, {
                iusGid: 'U5MR_F',
                sGid: 'Female',
                sName: 'Female'
            }]
        }, {
            uGid: '2',
            uName: 'Deaths Per 100 live births',
            subgroupList: [{
                iusGid: 'U5MR_T',
                sGid: 'Total',
                sName: 'Total'
            }, {
                iusGid: 'U5MR_M',
                sGid: 'Male',
                sName: 'Male'
            }, {
                iusGid: 'U5MR_F',
                sGid: 'Female',
                sName: 'Female'
            }]
        }]
    },
    {
        iGid: 'MMR',
        iName: 'Maternal Mortality Rate',
        unitList: []
    }]

    $scope.showSubgroup = function (ius) {
        if (ius.unitList.length > 0) {
            ius.selected = !ius.selected;
        } else {

            alert('get indicator details for gid ' + ius.iGid);

            ius.unitList = [{
                uGid: '1',
                uName: 'Deaths Per 1000 live births',
                subgroupList: [{
                    iusGid: 'IMR_T',
                    sGid: 'Total',
                    sName: 'Total'
                }, {
                    iusGid: 'IMR_M',
                    sGid: 'Male',
                    sName: 'Male'
                }, {
                    iusGid: 'IMR_F',
                    sGid: 'Female',
                    sName: 'Female'
                }]
            }, {
                uGid: '2',
                uName: 'Deaths Per 100 live births',
                subgroupList: [{
                    iusGid: 'IMR_T',
                    sGid: 'Total',
                    sName: 'Total'
                }, {
                    iusGid: 'IMR_M',
                    sGid: 'Male',
                    sName: 'Male'
                }, {
                    iusGid: 'IMR_F',
                    sGid: 'Female',
                    sName: 'Female'
                }]
            }];

            ius.selected = !ius.selected;
        }
    }
} ])