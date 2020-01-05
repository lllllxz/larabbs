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

//Route::get('/', 'TopicsController@index')->name('root');
Route::get('/', function () {

    $view = 'pages.permission_denied';
//    $data = compact('user');
    $to = '1559449141@qq.com';
    $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";
    \Illuminate\Support\Facades\Mail::send($view, [], function ($message) use ($to, $subject) {
        $message->to($to)->subject($subject);
    });

    echo 'Sended';
});

// 用户身份验证相关的路由
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// 用户注册相关路由
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// 密码重置相关路由
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

// Email 认证相关路由
Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify');
Route::post('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');

// User
Route::resource('users', 'UserController', ['only' =>['show', 'update', 'edit']]);

// Topic
Route::resource('topics', 'TopicsController', ['only' => ['index', 'create', 'store', 'update', 'edit', 'destroy']]);
Route::get('topics/{topic}/{slug?}', 'TopicsController@show')->name('topics.show');
Route::post('upload_image', 'TopicsController@uploadImage')->name('topics.upload_image');

// Category
Route::resource('categories', 'CategoryController', ['only' => ['show']]);

Route::resource('replies', 'RepliesController', ['only' => ['store', 'destroy']]);

//通知列表
Route::resource('notifications', 'NotificationsController', ['only' => 'index']);

Route::get('permission-denied', 'PageController@permissionDenied')->name('permission-denied');
