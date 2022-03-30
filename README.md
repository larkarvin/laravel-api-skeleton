## Installation

```
composer install
php artisan migrate
php artisan scribe:generate
```

Run the server

```
php artisan serve
```

## API Documentation

Generated using scribe

URL: `/docs`

## API Versioning

Codes and Routes are using separate folders

##### Folders

-   app/Http/Controllers/API/V1
-   app/Http/Controllers/API/V2

##### Routes

-   routes/api_v1.php
-   routes/api_v2.php

##### Changing the latest version

Edit `config/app.php` and modify

```
'api_latest'  => '1',
```

##### Route Prefix

You can modify the route api prefix in `RouteServiceProvide.php`

## ERROR HANDLING

1. Implemented custom handler to send Exception as JSON Response
2. Validation Errors are caught and send out as an JSON with a list of errors
3. Exception StackTrace are shown if `app.debug` is `enabled`

See `app/Exception/Handler.php`

## Data Validation Segragation

There are two types of data validation

1. Resource Data Validation Rules
2. Request Parameter Validation

##### Data Validation Rules

All Controller methods are using FormRequest to validate data before it hits the controller. If it throws an error the API will automatically transform the Exception to a JSON response.

##### Query Parameter Validation

Some methods has controls that uses queryParams and needs to be validated.

Since Laravel's FormRequest class does cannot validate QueryParams, I've added an a code that adds the queryParams to `$request->all()` in order for the validation rules to work

see `app\Http\Requests\AbstractRequest.php`

##### Filtering, and Sorting Separation

To separate adding of filters, and sorting to Resources. I've added a class for Searching. It automatically applies all eloquent filters available to the resource

see `app\Http\Search\TaskSearch.php`

in the controller it will look like this

```
// apply filters available in the request object
$query = (new TaskSearch)->apply($request);
$data = new TaskCollection($query->paginate(2)->withQueryString());
```

## Resource Ownership Policy

1. All Task are checked if the current logged in user is the owner

See `app/Policies/TaskPolicy.php`

## Media File

see `spatie/laravel-medialibrary`

## Tags

see `spatie/laravel-tags`

## Feedback

1. INVALID TAGS are not defined

## TO DO

1. Improve media and tags CRUD

```
/task/{task}/tags
/task/{task}/media
```

