<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::namespace('Api\Dashboard')->middleware('setLocale')->group(function () {
    Route::namespace('Auth')->group(function () {
        Route::post('login', 'AuthController@login');
    });

    Route::group(['middleware' => ['auth:api', 'admin']], function () {
        Route::namespace('Auth')->group(function () {
            Route::post('logout', 'AuthController@logout');
        });

        Route::namespace('Profile')->group(function () {
            Route::get('profile', 'ProfileController@index');
            Route::post('profile', 'ProfileController@update');
            Route::post('update_password', 'ProfileController@updatePassword');
        });

        Route::namespace('Country')->group(function () {
            Route::apiResource('countries', 'CountryController');
            Route::get('countries/{country}/cities', 'CountryController@getCities');
            Route::get('countries/{country}/cities_without_pagination', 'CountryController@getCitiesByCountryWithoutPagination');
            Route::get('countries_without_pagination', 'CountryController@getCountriesWithoutPagination');
        });

        Route::namespace('City')->group(function () {
            Route::apiResource('cities', 'CityController');
            Route::get('cities_without_pagination', 'CityController@getCitiesWithoutPagination');
        });

        Route::namespace('Category')->group(function () {
            Route::apiResource('categories', 'CategoryController');
        });

        Route::namespace('Setting')->group(function () {
            Route::apiResource('settings', 'SettingController');
        });

        Route::namespace('Admin')->group(function () {
            Route::apiResource('admins', 'AdminController');
        });

        Route::namespace('Client')->group(function () {
            Route::apiResource('client', 'ClientController');
            Route::get('clients/without-pagination', 'ClientController@clientsWithoutPagination');
        });

        Route::namespace('Notification')->group(function () {
            Route::apiResource('notifications', 'NotificationController')->except('update');
        });

        Route::namespace('Setting')->group(function () {
            Route::apiResource('settings', 'SettingController');
        });

        Route::namespace('Contact')->group(function () {
            Route::apiResource('contacts', 'ContactController')->except(['store', 'update']);
            Route::post('contacts/{contact}/reply', 'ContactController@reply');
        });

        Route::namespace('Tender')->group(function () {
            Route::apiResource('tenders', 'TenderController');
            Route::delete('tender/{tender}/medias/{media}', 'TenderController@deleteTenderMedia');
            Route::patch('tender/{tender}/change-status', 'TenderController@changeStatus');
        });

        Route::namespace('Expiration')->group(function () {
            Route::apiResource('expirations', 'ExpirationController');
            Route::delete('expiration/{expiration}/medias/{media}', 'ExpirationController@deleteExpirationMedia');
            Route::patch('expiration/{expiration}/change-status', 'ExpirationController@changeStatus');
        });

        Route::namespace('Agent')->group(function () {
            Route::apiResource('agents', 'AgentController');
            Route::delete('agent/{agent}/medias/{media}', 'AgentController@deleteAgentMedia');
            Route::patch('agent/{agent}/change-status', 'AgentController@changeStatus');
        });

        Route::namespace('Job')->group(function () {
            Route::apiResource('jobs', 'JobController');
            Route::delete('job/{job}/medias/{media}', 'JobController@deleteJobMedia');
            Route::patch('job/{job}/change-status', 'JobController@changeStatus');
        });

        Route::namespace('Package')->group(function () {
            Route::apiResource('packages', 'PackageController');
        });

        Route::namespace('OurServices')->group(function () {
            Route::apiResource('our-services', 'OurServicesController');
        });

        Route::namespace('MyClient')->group(function () {
            Route::apiResource('my-client', 'MyClientController');
        });

        Route::namespace('Subscription')->group(function () {
            Route::get('subscriptions', 'SubscriptionController@index');
            Route::post('subscription/{id}/change-status', 'SubscriptionController@changeStatus');
        });
    });
});
