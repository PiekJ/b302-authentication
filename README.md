# b302-authentication
Enables you to quickly install authentication pages and authorization.

## Install

Before installing, make sure your database and mail settings are correct.

To install this package, follow the steps below:

1. add ``"piek-j/b302-authentication": "~1.0"`` to your composer.json;
2. run the command ``composer update`` to install the package;
3. add the service provider ``PiekJ\B302Authentication\B302AuthenticationServiceProvider`` to your app.php config;
4. change the model in your auth.php config to ``PiekJ\B302Authentication\User``;
5. run the following command ``php artisan b302-auth:publish --config --view
~~5. run the following commands ``php artisan config:publish zizaco/confide`` and ``php artisan config:publish zizaco/entrust``;
6. change the role in the ``config/package/zizaco/entrust/config.php`` to ``PiekJ\B302Authentication\Role``;
7. change the permission in the ``config/package/zizaco/entrust/config.php`` to ``PiekJ\B302Authentication\Permission``;
8. runt he following commands ``php artisan b302-auth:migration`` and ``php artisan b302-auth:create-user``.
~~8. run the following commands ``php artisan b302-auth:migration`` (type "Y" to confirm), ``php artisan migrate`` and ``php artisan b302-auth:create-user``.

Go visit http://yoururl/users/login to login with the newly created user (email: admin@admin.nl, password: admin).

## Guide

### Urls

/users/login
/users/create
/users/forgot_password
/users/reset_password/{{token}}

### Updating templates

You can use the command ``php artisan view:publish piek-j\b302-authentication`` to copy the files to ``app/views/packages/piek-j/b302-authentication`` where you can edit them.

### More information

Authentication, take a look at [Zizaco/Confide](https://github.com/Zizaco/confide).

Role and permissions, take a look at [Zizaco/Entrust](https://github.com/Zizaco/entrust/tree/1.0).