<?php
Route::get('/module/add', 'ModuleController@add')->name('module.add');
Route::get('/module/index', 'ModuleController@index')->name('module.index');
Route::get('/module/list', 'ModuleController@list')->name('module.list');
Route::get('/module/{id}/edit', 'ModuleController@edit')->name('module.edit');

Route::post('/module/create', 'ModuleController@create')->name('module.create');
Route::post('/module/update', 'ModuleController@update')->name('module.update');
Route::put('/module/update', 'ModuleController@update')->name('module.update');


Route::post('/module/delete', 'ModuleController@delete')->name('module.delete');

Route::post('/module/{id}/enable', 'ModuleController@enable')->name('module.enable');

//参数配置,跳转到模块管理首页
Route::get('/areaModule', 'ModuleController@index')->name('areaModule.index');