<?php

Route::get('/taskManage/index', 'PcTaskController@index')->name('taskManage.index');
Route::get('/taskManage/list', 'PcTaskController@list')->name('taskManage.list');

