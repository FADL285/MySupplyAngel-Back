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
    });
});
