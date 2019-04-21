# Rest API with Laravel 5.7

A RESTful API boilerplate for Laravel 5.7. Features included:

- Users Resource
- OAuth2 Authentication using Laravel Passport
- Scope based Authorization
- Validation
- Pagination
- Seeding Database With Model Factory
- Event Handling
- Sending Mail using Mailable class
- Endpoint Tests

## Required

* composer  
* php(version>=7.2)  
* mysql  

### Install Laravel
```
$ composer create-project laravel/laravel rest_api
```

### Install passport
```
$ composer require laravel/passport
```

### Run the Artisan migrate command:
```bash
$ php artisan migrate
```

#### Create "personal access" and "password grant" clients which will be used to generate access tokens:
```bash
$ php artisan passport:install
```

#### Use HasApiTokens in user model for authentication:
```bash
use Laravel\Passport\HasApiTokens
```

You can find those clients in ```oauth_clients``` table.

### API Routes

| HTTP Method	| Path | Action | Scope | Desciption  |
| ----- | ----- | ----- | ---- |------------- |
| GET      | /users | index | users:list | Get all users
| POST     | /users | store | users:create | Create an user
| GET      | /users/{user} | show | users:read |  Fetch an user by id
| PUT      | /users/{user} | update | users:write | Update an user by id
| DELETE   | /users/{user} | destroy | users:delete | Delete an user by id

Note: ```users/me``` is a special route for getting current authenticated user.
And for all User routes 'users' scope is available if you want to perform all actions.
