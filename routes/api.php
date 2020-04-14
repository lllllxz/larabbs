<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->namespace('Api')->name('api.v1.')->group(function() {

    //登录相关接口
    Route::middleware('throttle:'.config('app.rate_limits.sign'))->group(function () {
        // 短信验证码
        Route::post('verificationCodes', 'VerificationCodesController@store')->name('verificationCodes.store');
        //用户注册
        Route::post('users', 'UsersController@store')->name('users.store');
        //图片验证码
        Route::post('captchas', 'CaptchasController@store')->name('captchas.store');

        // 小程序用户注册
        Route::post('weapp/users', 'UsersController@weappStore')->name('api.weapp.users.store');
        //微信小程序登录
        Route::post('weapp/authorizations', 'AuthorizationsController@weappStore')->name('api.weapp.authorizations.store');

        //第三方登录
        Route::post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
            ->where('social_type', 'weixin') //'weixin|weibo'
            ->name('api.socials.authorizations.store');
        //登录
        Route::post('authorizations', 'AuthorizationsController@store')->name('api.authorizations.store');
        //刷新token
        Route::put('authorizations/current', 'AuthorizationsController@update')->name('api.authorizations.update');
        //退出登录（删除token）
        Route::delete('authorizations/current', 'AuthorizationsController@destroy')->name('api.authorizations.destroy');
    });

    //其他接口
    Route::middleware('throttle:'.config('app.rate_limits.access'))->group(function () {
        //游客访问接口
        Route::get('users/{user}', 'UsersController@show')->name('users.show');
        //话题分类列表
        Route::get('categories', 'CategoriesController@index')->name('categories.index');
        //话题列表
        Route::resource('topics', 'TopicsController')->only(['index', 'show']);
        //查看某用户的话题
        Route::get('users/{user}/topics', 'TopicsController@userIndex')->name('users.topics.index');
        //话题回复列表
        Route::get('topics/{topic}/replies', 'RepliesController@index')->name('topics.replies.index');
        //查看某用户的回复
        Route::get('users/{user}/replies', 'RepliesController@userIndex')->name('users.replies.index');
        // 资源推荐
        Route::get('links', 'LinksController@index')->name('links.index');
        // 活跃用户
        Route::get('actived/users', 'UsersController@activedIndex')->name('users.actived.index');

        /*
         |-----------------------------------------------------------------------------
         | 登录后访问接口
         |----------------------------------------------------------------------------
         */
        Route::middleware('auth:api')->group(function (){
            //返回当前用户信息
            Route::get('user', 'UsersController@me')->name('user.show');
            //更新用户信息
            Route::patch('user', 'UsersController@update')->name('user.update.patch');
            //weapp更新用户信息
            Route::put('user', 'UsersController@update')->name('user.update.put');
            //上传图片
            Route::post('images', 'ImagesController@store')->name('images.store');
            //发布话题
            Route::resource('topics', 'TopicsController')->only(['store', 'update', 'destroy']);
            //回复话题
            Route::post('topics/{topic}/replies', 'RepliesController@store')->name('replies.store');
            //删除回复
            Route::delete('topics/{topic}/replies/{reply}', 'RepliesController@destroy')->name('replies.destroy');
            //回复通知
            Route::get('notifications', 'NotificationsController@index')->name('notifications.index');
            //通知数量
            Route::get('notifications/stats', 'NotificationsController@stats')->name('notificaitons.stats');
            //标记通知为已读
            Route::patch('user/read/notifications', 'NotificationsController@read')->name('user.notifications.read');
            Route::put('user/read/notifications', 'NotificationsController@read')->name('user.notifications.read.put');
            //当前用户的权限
            Route::get('user/permissions', 'PermissionsController@index')->name('user.permissions.index');
            //当前用户的身份
            Route::get('user/roles', 'RolesController@index')->name('user.roles.index');
        });
    });


});

