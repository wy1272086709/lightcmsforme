<?php
Route::get('/userManage', 'UserManageController@index')->name('userManage.index');
Route::get('/userManage/list', 'UserManageController@list')->name('userManage.list');
Route::get('/userManage/{id}/edit', 'UserManageController@edit')->name('userManage.edit');
Route::get('/userManage/add', 'UserManageController@add')->name('userManage.add');
Route::post('/userManage/create', 'UserManageController@create')->name('userManage.create');

Route::post('/userManage/update', 'UserManageController@update')->name('userManage.update');
Route::put('/userManage/update', 'UserManageController@update')->name('userManage.update');

Route::post('/userManage/delete', 'UserManageController@delete')->name('userManage.delete');