<?php

Route::prefix('avanatest')->group(function () {
    Route::get('/view-data', 'Avanahuda\Avanatest\Controllers\TestController@viewData');
    Route::get('/timezones/{timezone}', 'Avanahuda\Avanatest\Controllers\TestController@index');

    Route::prefix('order')->group(function () {
        Route::get('/', 'Avanahuda\Avanatest\Controllers\OrderController@index');
        Route::post('/create', 'Avanahuda\Avanatest\Controllers\OrderController@store');
        Route::post('/edit/{id}', 'Avanahuda\Avanatest\Controllers\OrderController@update');
        Route::get('/delete/{id}', 'Avanahuda\Avanatest\Controllers\OrderController@destroy');
    });
    Route::prefix('order-detail')->group(function () {
        Route::get('/', 'Avanahuda\Avanatest\Controllers\OrderDetailController@index');
        Route::post('/create', 'Avanahuda\Avanatest\Controllers\OrderDetailController@store');
        Route::get('/show/{id}', 'Avanahuda\Avanatest\Controllers\OrderDetailController@show');
        Route::put('/edit/{id}', 'Avanahuda\Avanatest\Controllers\OrderDetailController@update');
        Route::get('/delete/{id}', 'Avanahuda\Avanatest\Controllers\OrderDetailController@destroy');
    });
    Route::prefix('customer')->group(function () {
        Route::get('/', 'Avanahuda\Avanatest\Controllers\CustomerController@index');
        Route::get('/create', 'Avanahuda\Avanatest\Controllers\CustomerController@store');
        Route::get('/show/{id}', 'Avanahuda\Avanatest\Controllers\CustomerController@show');
        Route::put('/edit/{id}', 'Avanahuda\Avanatest\Controllers\CustomerController@update');
        Route::get('/delete/{id}', 'Avanahuda\Avanatest\Controllers\CustomerController@destroy');
    });
    Route::prefix('payment')->group(function () {
        Route::get('/', 'Avanahuda\Avanatest\Controllers\PaymentController@index');
        Route::get('/create', 'Avanahuda\Avanatest\Controllers\PaymentController@store');
        Route::put('/edit/{id}', 'Avanahuda\Avanatest\Controllers\PaymentController@update');
        Route::get('/delete/{id}', 'Avanahuda\Avanatest\Controllers\PaymentController@destroy');
    });
});

