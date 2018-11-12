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

Route::get('/', function () {
    return view('welcome');
});


Auth::routes();

Route::view('scan','scan');

Route::get('/home', 'HomeController@index')->name('home');

Route::get('threads', 'ThreadsController@index')->name('threads');;                    //帖子列表
Route::get('threads/create', 'ThreadsController@create');                              //新建帖子
Route::get('threads/{channel}/{thread}', 'ThreadsController@show');                    //帖子详情
Route::delete('threads/{channel}/{thread}', 'ThreadsController@destroy');              //删除帖子
Route::post('threads', 'ThreadsController@store');                                     //保存帖子->middleware('must-be-confirmed')
Route::patch('threads/{channel}/{thread}','ThreadsController@update');                 //更新话题
Route::get('threads/{channel}', 'ThreadsController@index');                            //分类下的帖子

Route::get('search','SearchController@show');                                  //话题搜索

Route::post('locked-threads/{thread}','LockedThreadsController@store')->name('locked-threads.store')->middleware('admin');  //锁定话题
Route::delete('locked-threads/{thread}','LockedThreadsController@destroy')->name('locked-threads.destroy')->middleware('admin'); //解除锁定

Route::post('/threads/{channel}/{thread}/replies', 'RepliesController@store');         //帖子的回复
Route::get('/threads/{channel}/{thread}/replies', 'RepliesController@index');          //查看评论
Route::patch('/replies/{reply}', 'RepliesController@update');                          //更新评论
Route::delete('/replies/{reply}', 'RepliesController@destroy')->name('replies.destroy');                         //删除评论
Route::post('/replies/{reply}/best', 'BestRepliesController@store')->name('best-replies.store');                   //最佳评论

Route::post('/threads/{channel}/{thread}/subscriptions','ThreadSubscriptionsController@store')->middleware('auth');           //订阅帖子
Route::delete('/threads/{channel}/{thread}/subscriptions','ThreadSubscriptionsController@destroy')->middleware('auth');       //取消订阅


Route::post('/replies/{reply}/favorites','FavoritesController@store');                //点赞
Route::delete('/replies/{reply}/favorites','FavoritesController@destroy');            //取消点赞


Route::get('/profiles/{user}','ProfilesController@show')->name('profile');            //个人中心

Route::get('/profiles/{user}/notifications','UserNotificationsController@index');                           //消息通知
Route::delete('/profiles/{user}/notifications/{notification}','UserNotificationsController@destroy');       //清除通知

Route::get('/register/confirm','Auth\RegisterConfirmationController@index')->name('register.confirm');           //用户注册邮箱认证

Route::get('api/users','Api\UsersController@index');                                  //@某人获取数据
Route::post('api/users/{user}/avatar','Api\UserAvatarController@store')->middleware('auth')->name('avatar');   //上传头像
