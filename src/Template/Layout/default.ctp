<!DOCTYPE html>
<html>
    <head>
        <?php echo $this->Html->charset() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <title>DevInfo Database Administrative Tool</title>
        <?php echo $this->Html->meta('icon') ?>
        <?php echo $this->Html->css(['reset','style','responsive','font-awesome.min','ngDialog']) ?>
        <?php
            echo $this->Html->script(['lib/jquery-2.1.4.min','lib/angular.min',
            'app/shared/angular-ui-router.min','app/shared/ngDialog.min', 
            'app/components/login/login.module', 'app/components/login/login.controller', 
            'app/components/database/database.module', 'app/components/database/database.controller', 'app/components/database/database.service',
            'app/components/databaseManagement/databaseManagement.module','app/components/databaseManagement/databaseManagement.controller','app/components/databaseManagement/databaseManagement.service',
             'app/components/userManagement/userManagement.module','app/components/userManagement/userManagement.controller','app/components/userManagement/userManagement.service',
            'app/appConfig','app/app','app/app.controller','app/app.config','app/app.constant', 'app/app.service'])
        ?>
    </head>
    <body ng-controller="appController">
        <header class="main-header darkblue" ui-view="header">
        </header>
        <section class="main">
            <div ui-view="content">
            </div>
        </section>
        <footer class="footer darkblue" ui-view="footer">
        </footer>
    </body>
</html>
