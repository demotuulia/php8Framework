# Match Tools with php8 framework

## Introduction

This an example framework with php8 which is mainly done by my own code.

There is  application is a similar application with the Match Tools API which I have done with Laravel 10.

See more info in
[https://github.com/demotuulia/Laravel10_MatchToolsModule](https://github.com/demotuulia/Laravel10_MatchToolsModule)
and
[https://github.com/demotuulia/VueJs_MatchToolsModuleClient](https://github.com/demotuulia/VueJs_MatchToolsModuleClient)

This framework has been as a Word Press plugin originally on a php7 server on the same server with the main site, so I needed
a light framework, which I can self customize.
This demo shows some features of the framework and there are only some essential files to show the php8 framework.

## Structure and code

This framework has the main components and follows the MVC  structure (In stead of HTML view we have json response).


The code has been made after the Php PSR standards and inspected by PHPStan and Code Sniffer.


```
App
  Controllers
    Standard MVC controllers
  Enums
    Some enum constants
  Factory
    Factory classes to create objects like models, serves and request types
  Helpers
    Miscellaneous helpers
  Lib
    Miscellaneous php classes
  Models
    ORM models for each table
  Plugins
    Third party plugins
  Services
    Service classes
  Traits
    Traits to be used in many classes
Commands
    Cli commands
Public
    API public point
Routes
    Routes configuration
Tests
    Tests
Translations
    Translation files
```


## Endpoints

Some example endpoints are documented by Postman on the following link
[https://www.postman.com/navigation-physicist-32016049/php8matchtools/overview](https://www.postman.com/navigation-physicist-32016049/php8matchtools/overview)




## Facilities

Below are explained some of the facilities of the framework.

### Configuration

The configuration is located in the file

config/config.php

See 
[config/config_sample.php](https://github.com/demotuulia/php8Framework/blob/main/config/config_sample.php)

The helper HConfig uses it.


[App\Helpers](https://github.com/demotuulia/php8Framework/blob/main/App/Helpers/HConfig.php)

Example:

```
$apiHost = HConfig::getConfig('api_host');
```

### Log statements

With this helper you can write log statements. This version writes them to the database.
This can extended for example to files if needed.

The helper  [App\Helpers\HLogStatement](https://github.com/demotuulia/php8Framework/blob/main/App/Helpers/HLogStatement.php)  
does this.

To enable the log statements you need to configure the by
```setLogStatemnts => true```

Example:

```
   HLogStatement::set('Failed e-mail hash request by the e-mail ' .  $request['email']);
```

### Request content types

* This API handles automatically the following request content types:
  application/json
* application/x-www-form-urlencoded
* multipart/form-data

The following classes and traits are responsible for this:


[App/Traits/TRequest](https://github.com/demotuulia/php8Framework/blob/main/App/Traits/TRequest.php) 
[App/Factory/FRequest](https://github.com/demotuulia/php8Framework/blob/main/App/Factory/FRequest.php)
[App/Services/Request](https://github.com/demotuulia/php8Framework/tree/main/App/Services/Request)


### User Management

There are the following facilities

* Create and edit users
* User roles
* Login and logout
* In case the password is forgotten send hashed login link to email.

The test below describes the use of this:
[AdminUsersControllerTest.php](https://github.com/demotuulia/php8Framework/blob/main/Tests/Integration/AdminUsersControllerTest.php)


### Authorization

This framework supports authorization different user roles.
In the matches tools project we have the  roles 'quest', 'administrator' and 'application administrator'.
The configuration file

[config/authorizations/list.php](https://github.com/demotuulia/php8Framework/blob/main/config/authorizations/list.php)



defines the authorizations for the roles 'quest'
and 'application administrator'.  The role 'Admin' is allowed to do everything.

The function`'pageAllowed'`in the file

[TAuthenticate::pageAllowed](https://github.com/demotuulia/php8Framework/blob/10622046af9ccdc45de507442050b2bf4e8c226e/App/Traits/TAuthenticate.php#L37)

checks the authorization

### Form validator

In Laravel style you can create standard and customized validation rules per controller and action.
Like:

* required
* type
* password
* email format
* value found in a certain database table
* range

Se example of the validation and the validation trait:
[MatchesProfilesController::getValidationRules](https://github.com/demotuulia/php8Framework/blob/10622046af9ccdc45de507442050b2bf4e8c226e/App/Controllers/AdminUsersController.php#L172)

[App/Traits/TValidate](https://github.com/demotuulia/php8Framework/blob/main/App/Traits/TValidate.php)


### Tests

There are unit tests and integration tests.
The feature tests serve also as documentation and should be up to date.

The main focus has been in the integration tests, which make a curl request to the API.

Examples:

[Unit test for translations](https://github.com/demotuulia/php8Framework/blob/main/Tests/Feature/TRanslationsTest.php) 

[Integration test for Admin Users ](https://github.com/demotuulia/php8Framework/blob/main/Tests/Integration/AdminUsersControllerTest.php)

### ORM

With this ORM each database table has its own model and service class.
With this ORM managed to make the Match tools without any direct SQL statements. All of the queries are built
by the ORM.

The model classes define the properties of the table, like to columns and relations to
the other tables.
The model classes are inherited from the

[App/Models/BaseModel](https://github.com/demotuulia/php8Framework/blob/main/App/Models/BaseModel.php) 
which uses the helper and are responsible to build te SQL queries.
[App/Models/Traits/TBaseModelHelpers](https://github.com/demotuulia/php8Framework/blob/main/App/Models/Traits/TBaseModelHelpers.php) 


The service classes are responsible for the business rules of each table.
The controllers are calling the functions from the service classes.

Examples

We have 2 tables users and profiles. Each user can have one or more profiles.
They have one to many relation below.

```
-----------------         --------------------
|matches_users |         /|  matches_profile |       
|id  (pk)      | ------ --| user_id (fk)     |
|              |         \|                  |
----------------           -------------------
```

The related models and service classesare found in


[App/Models/MatchesUsers](https://github.com/demotuulia/php8Framework/blob/main/App/Models/MatchesUsers.php)

[App/Models/MatchesProfile](https://github.com/demotuulia/php8Framework/blob/main/App/Models/MatchesProfile.php) 

[App\Services\AdminUsersService](https://github.com/demotuulia/php8Framework/blob/main/App/Services/AdminUsersService.php)

[App\Services\MatchesUsersService](https://github.com/demotuulia/php8Framework/blob/main/App/Services/MatchesUsersService.php)

Some code examples:

```
<?
    /** @var MatchesUsers $model */
    $model = FModel::build('MatchesUsers');

    // Get one user by id
    $result = $model->get(1);
    /**  result  (only the essential columns are shown):
      [
        0 => [
          'id' => 1,
          'name' => 'Elias van de Pol'
        ],
      ]
    **/


   // Get one user by name
    $result = $model->get('Jonah de Boer', ['column' => 'f0_naam']);
    /**  result (only the essential columns are shown):
      [
        0 => [
          'id' => 54,
          'name' => 'Jonah de Boer',
        ],
      ]
    **/

   // get user having the string 'on' in their name and get their profiles
    $options = [
            'wildcard' =>  [
                'needle' => 'on',
                'columns' => 'f0_name', // there can be several columns separated by comma
            ],
            'with' => ['profiles']
    ];
     $result = $model->get(null, $options);

 /**  result  (only the essential columns are shown):
  [
    0 => [
      'id' => 6,
      'name' => 'Mason Blom',
      'profiles' => [
        0 => [
          'id' => 12,
          'user_id' => 6,
          'name' => 'Mason Blom profile 1',
        ],
        1 => [
          'id' => 13,
          'user_id' => 6,
          'name' => 'Mason Blom profile 2',
        ],
      ],
    ],
    1 => [
      'id' => 24,
      'name' => 'Jill de Koning',
      'profiles' => [
        0 => [
          'id' => 45,
          'matches_form_id' => 3,
          'name' => 'Jill de Koning',
        ],
      ],
    ],
  
  ]
**/
```


### Translations

You can make the application multilingual by the translations
The service class App\Services\TranslationsService is responsible for this.
[App\Services\TranslationsService](https://github.com/demotuulia/php8Framework/blob/main/App/Services/TranslationsService.php) 


The test below describes the usage of it
[Tests/Feature/TRanslationsTest](https://github.com/demotuulia/php8Framework/blob/main/Tests/Feature/TRanslationsTest.php) 

The translations are configures in the folder Translations
See example
[translations/nl_NL/errors.php](https://github.com/demotuulia/php8Framework/blob/main/translations/nl_NL/errors.php)



