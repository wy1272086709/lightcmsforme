<?php
Route::get('/taskGroup/add', 'TaskGroupController@add')->name('taskGroup.add');
Route::get('/taskGroup/index', 'TaskGroupController@index')->name('taskGroup.index');
Route::get('/taskGroup/list', 'TaskGroupController@list')->name('taskGroup.list');

Route::get('/taskGroup/{id}/edit', 'TaskGroupController@edit')->name('taskGroup.edit');
Route::post('/taskGroup/create', 'TaskGroupController@create')->name('taskGroup.create');
Route::post('/taskGroup/update', 'TaskGroupController@update')->name('taskGroup.update');
Route::post('/taskGroup/delete', 'TaskGroupController@destroy')->name('taskGroup.delete');

Route::put('/taskGroup/update', 'TaskGroupController@update')->name('taskGroup.update');