<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/getimagenes','files\archivosController@getImages');
Route::post('/subirimagenes','files\archivosController@uploadImage');
Route::delete('/deleteimagenes/{id}','files\archivosController@deleteImage');

