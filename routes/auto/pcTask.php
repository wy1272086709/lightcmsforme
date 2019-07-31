<?php
Route::get('/pcTask/add', 'PcTaskController@add')->name('pcTask.add');
Route::get('/pcTask/{id}/edit', 'PcTaskController@edit')->name('pcTask.edit');
Route::post('/pcTask/create', 'PcTaskController@create')->name('pcTask.create');
Route::post('/pcTask/update', 'PcTaskController@update')->name('pcTask.update');
Route::post('/pcTask/delete', 'PcTaskController@destroy')->name('pcTask.delete');
Route::post('/pcTask/stop', 'PcTaskController@stop')->name('pcTask.stop');
Route::get('/pcTask/area', 'PcTaskController@getAreaInfo')->name('pcTask.area');



Route::get('/pcTask/index', 'PcTaskController@index')->name('pcTask.index');
Route::post('/pcTask/list', 'PcTaskController@list')->name('pcTask.list');
Route::get('/pcTask/list', 'PcTaskController@list')->name('pcTask.list');
Route::get('/pcTask/test', 'PcTaskController@test')->name('pcTask.test');