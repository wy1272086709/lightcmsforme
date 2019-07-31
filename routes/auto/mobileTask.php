<?php
Route::get('/mobileTask/add', 'MobileTaskController@add')->name('mobileTask.add');
Route::get('/mobileTask/{id}/edit', 'MobileTaskController@edit')->name('mobileTask.edit');
Route::get('/mobileTask/index', 'MobileTaskController@index')->name('mobileTask.index');

Route::post('/mobileTask/create', 'MobileTaskController@create')->name('mobileTask.create');
Route::post('/mobileTask/update', 'MobileTaskController@update')->name('mobileTask.update');
