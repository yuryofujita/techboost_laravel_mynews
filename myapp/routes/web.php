<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/news_test/{id}', function ($id) {
    $a = App\News::find($id)->histories;
    // dd($a);
    return $a;
});

Route::get('/about', function () {
    $user = new App\User();
    $ruesult = $user->selectUser();
    dd($ruesult);
    return $ruesult;
});


// Route::group(['prefix' => 'admin'], function() {
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function() {
    Route::get('news/create', 'Admin\NewsController@add');
    Route::post('news/create', 'Admin\NewsController@create');
    Route::get('news', 'Admin\NewsController@index');
    Route::get('news/edit', 'Admin\NewsController@edit');
    Route::post('news/edit', 'Admin\NewsController@update');
    Route::get('news/delete', 'Admin\NewsController@delete');

    Route::get('profile/create', 'Admin\ProfileController@add');
    Route::get('profile/create', 'Admin\ProfileController@add');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/', 'NewsController@index');
