<?php
Route::get('/intervalTime/index', 'IntervalTimeController@index')->name('intervalTime.index');
Route::post('/intervalTime/set', 'IntervalTimeController@store')->name('intervalTime.store');