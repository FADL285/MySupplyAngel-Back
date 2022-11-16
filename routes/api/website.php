<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::namespace('Api\WebSite')->middleware('setLocale')->group(function () {
    Route::namespace('Auth')->group(function () {
        Route::post('login', 'AuthController@login');
        Route::post('register', 'AuthController@register');
        Route::post('verify', 'AuthController@verify');
        Route::post('resendCode', 'AuthController@resendCode');

        Route::post('forgot-password', 'AuthController@forgotPassword');
        Route::post('check-code', "AuthController@checkCode");
        Route::patch('reset-password', "AuthController@resetPassword");
    });

    Route::group(['middleware' => ['auth:api']], function () {
        Route::namespace('Auth')->group(function () {
            Route::post('logout', 'AuthController@logout');
        });

        Route::namespace('Country')->withoutMiddleware('auth:api')->group(function () {
            Route::apiResource('countries', 'CountryController');
            Route::get('countries-without-pagination', 'CountryController@withoutPagination');
        });

        Route::namespace('City')->withoutMiddleware('auth:api')->group(function () {
            Route::apiResource('cities', 'CityController');
            Route::get('cities-without-pagination', 'CityController@withoutPagination');
        });

        Route::namespace('Category')->withoutMiddleware('auth:api')->group(function () {
            Route::apiResource('categories', 'CategoryController');
            Route::get('categories-without-pagination', 'CategoryController@withoutPagination');
        });

        Route::namespace('Profile')->group(function () {
            Route::get('profile', 'ProfileController@index');
            Route::patch('profile/edit', 'ProfileController@editProfile');
            Route::patch('profile/edit-phone', 'ProfileController@editPhone');
            Route::patch('profile/edit-email', 'ProfileController@editEmail');
            Route::patch('profile/edit-password', 'ProfileController@editPassword');
            Route::post('profile/previous-work', 'ProfileController@addPreviousWork');
            Route::delete('profile/previous-work', 'ProfileController@deletePreviousWork');
        });

        Route::namespace('Tender')->group(function () {
            Route::apiResource('tenders', 'TenderController')->except('index');
            Route::get('tenders', 'TenderController@index')->withoutMiddleware('auth:api');
            Route::get('my-tenders', 'TenderController@myTenders');
            Route::delete('tender/{tender}/medias/{media}', 'TenderController@deleteTenderMedia');
            Route::get('tenders/{tender}/offers', 'TenderOfferController@index');
            Route::get('tender/offers', 'TenderOfferController@myTenderOffers');
            Route::post('tenders/{tender}/offers', 'TenderOfferController@store');
            Route::delete('tenders/{tender}/offers/{offer}', 'TenderOfferController@destroy');
            Route::delete('tenders/{tender}/offers/{offer}/medias/{media}', 'TenderOfferController@deleteTenderOfferMedia');
            Route::put('tenders/{tender}/offers/{offer}', 'TenderOfferController@update');
            Route::get('tender/favorites', 'TenderController@favorite');
            Route::post('tenders/{tender}/favorite', 'TenderController@toggelToFavorite');
        });

        Route::namespace('Expiration')->group(function () {
            Route::apiResource('expirations', 'ExpirationController')->except(['index', 'show']);
            Route::get('expirations', 'ExpirationController@index')->withoutMiddleware('auth:api');
            Route::get('expirations/{expiration}', 'ExpirationController@show')->withoutMiddleware('auth:api');
            Route::get('my-expiration', 'ExpirationController@myExpirations');
            Route::delete('expiration/{expiration}/medias/{media}', 'ExpirationController@deleteExpirationMedia');
            Route::get('expiration/favorites', 'ExpirationController@favorite');
            Route::post('expiration/{expiration}/favorite', 'ExpirationController@toggelToFavorite');
        });

        Route::namespace('Agent')->group(function () {
            Route::apiResource('agents', 'AgentController')->except(['index', 'show']);
            Route::get('agents', 'AgentController@index')->withoutMiddleware('auth:api');
            Route::get('agents/{agent}', 'AgentController@show')->withoutMiddleware('auth:api');
            Route::get('my-agent', 'AgentController@myAgents');
            Route::delete('agent/{agent}/medias/{media}', 'AgentController@deleteAgentMedia');
            Route::get('agents/{agent}/offers', 'AgentOfferController@index');
            Route::get('agent/offers', 'AgentOfferController@myAgentOffers');
            Route::post('agents/{agent}/offers', 'AgentOfferController@store');
            Route::delete('agents/{agent}/offers/{offer}', 'AgentOfferController@destroy');
            Route::delete('agents/{agent}/offers/{offer}/medias/{media}', 'AgentOfferController@deleteAgentOfferMedia');
            Route::put('agents/{agent}/offers/{offer}', 'AgentOfferController@update');
            Route::get('agent/favorites', 'AgentController@favorite');
            Route::post('agent/{agent}/favorite', 'AgentController@toggelToFavorite');
        });

        Route::namespace('Job')->group(function () {
            Route::apiResource('jobs', 'JobController')->except(['indes', 'show']);
            Route::get('jobs', 'JobController@index')->withoutMiddleware('auth:api');
            Route::get('jobs/{job}', 'JobController@show')->withoutMiddleware('auth:api');
            Route::get('my-job', 'JobController@myJob');
            Route::delete('job/{job}/medias/{media}', 'JobController@deleteJobMedia');
            Route::get('job/favorites', 'JobController@favorite');
            Route::post('job/{job}/favorite', 'JobController@toggelToFavorite');
            Route::get('employees', 'JobController@employees')->withoutMiddleware('auth:api');
            Route::get('employees/{employee}', 'JobController@employee')->withoutMiddleware('auth:api');
            Route::patch('employees/need-job', 'JobController@needJob');
        });

        Route::namespace('Notification')->group(function () {
            Route::apiResource('notifications', 'NotificationController')->except(['update', 'store']);
            Route::delete('delete-all-notifications', 'NotificationController@deleteAllNotifications');
        });

        Route::namespace('Contact')->withoutMiddleware('auth:api')->group(function () {
            Route::get('contacts', 'ContactController@getContact');
            Route::post('contact-us', 'ContactController@contact');
        });

        Route::namespace('Home')->withoutMiddleware('auth:api')->group(function () {
            Route::get('home', 'HomeController@index');
        });

        Route::namespace('General')->withoutMiddleware('auth:api')->group(function () {
            Route::get('about', 'GeneralController@getAbout');
            Route::get('terms', 'GeneralController@getTerms');
            Route::get('privacy', 'GeneralController@getPrivacy');
            Route::get('why-us', 'GeneralController@getWhyUs');
        });
    });
});
