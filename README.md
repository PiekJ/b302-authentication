# b302-authentication
Enables you to quickly install authentication pages and authorization.

## Install

Before installing, make sure your database and mail settings are correct.

To install this package, follow the steps below:

1. add ``"piek-j/b302-authentication": "~1.0"`` to your composer.json;
2. run the command ``composer update`` to install the package;
3. add the service provider ``PiekJ\B302Authentication\B302AuthenticationServiceProvider`` to your app.php config;
4. change the model in your auth.php config to ``PiekJ\B302Authentication\User``;
5. run the command ``php artisan config:publish zizaco/entrust``;
6. change the role in the ``config/package/zizaco/entrust/config.php`` to ``PiekJ\B302Authentication\Role``;
7. change the permission in the ``config/package/zizaco/entrust/config.php`` to ``PiekJ\B302Authentication\Permission``;
8. run the following commands ``php artisan b302-auth:migration`` (type for every confirm ``Y``), ``php artisan migrate`` and ``php artisan b302-auth:create-user``.

Go visit http://yoururl/users/login to login with the newly created user (email: admin@admin.nl, password: admin).

## Guide

### Urls

| Url | Description |
| --- | --- |
| /users/login | Shows up the login form |
| /users/create | Shows up the signup form |
| /users/forgot_password | The form to request a password reset mail |
| /users/reset_password/{{token}} | Fill in your new password to reset |
| /users/confirm/{{token}} | Here the users confirm his account |

For more detailed urls view [routes.php](src/routes.php) and the [B302AuthUserController.php](src/controllers/B302AuthUsersController.php).

### Updating templates

You can use the command ``php artisan view:publish piek-j\b302-authentication`` to copy the files to ``app/views/packages/piek-j/b302-authentication`` where you can edit them.
To edit the views of the forms and emails use the following command ``php artisan view:publish zizaco/confide`` to copy the files to ``ap/views/packages/zizaco/confide`` where you can edit them.

### More information

Authentication, take a look at [Zizaco/Confide](https://github.com/Zizaco/confide).

Role and permissions, take a look at [Zizaco/Entrust](https://github.com/Zizaco/entrust/tree/1.0).