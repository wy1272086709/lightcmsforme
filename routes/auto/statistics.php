<?php
Route::get('/statistics/ippv_list', 'StatisticsController@list')->name('statistics.list');
Route::get('/statistics/hour_ippv/{id}', 'StatisticsController@ipPvHourView')->name('statistics.ipPvHourView');
Route::get('/statistics/hour_ippvList}', 'StatisticsController@ipPvHourList')->name('statistics.ipPvHourList');
Route::get('/statistics', 'StatisticsController@index')->name('statistics.index');