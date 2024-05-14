<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// **************************** FDH **********************************************
Route::match(['get','post'],'fdh_mini_dataset',[App\Http\Controllers\FdhController::class, 'fdh_mini_dataset'])->name('fdh.fdh_mini_dataset');
Route::match(['get','post'],'fdh_mini_dataset_api',[App\Http\Controllers\FdhController::class, 'fdh_mini_dataset_api'])->name('fdh.fdh_mini_dataset_api');
Route::match(['get','post'],'fdh_mini_dataset_pull',[App\Http\Controllers\FdhController::class, 'fdh_mini_dataset_pull'])->name('fdh.fdh_mini_dataset_pull');
Route::match(['get','post'],'fdh_mini_dataset_pullnoinv',[App\Http\Controllers\FdhController::class, 'fdh_mini_dataset_pullnoinv'])->name('fdh.fdh_mini_dataset_pullnoinv');
Route::match(['get','post'],'fdh_mini_dataset_apicliam',[App\Http\Controllers\FdhController::class, 'fdh_mini_dataset_apicliam'])->name('fdh.fdh_mini_dataset_apicliam');
Route::match(['get','post'],'fdh_mini_dataset_rep',[App\Http\Controllers\FdhController::class, 'fdh_mini_dataset_rep'])->name('fdh.fdh_mini_dataset_rep');
Route::match(['get','post'],'fdh_mini_dataset_pulljong',[App\Http\Controllers\FdhController::class, 'fdh_mini_dataset_pulljong'])->name('fdh.fdh_mini_dataset_pulljong');


Route::match(['get','post'],'authen_auth_new',[App\Http\Controllers\Auto_authenController::class, 'authen_auth_new'])->name('auto.authen_auth_new');//
Route::match(['get','post'],'authen_auth_tinew',[App\Http\Controllers\Auto_authenController::class, 'authen_auth_tinew'])->name('auto.authen_auth_tinew');//
Route::match(['get','post'],'pullauthen_spschnew',[App\Http\Controllers\Auto_authenController::class, 'pullauthen_spschnew'])->name('auto.pullauthen_spschnew');//

Route::match(['get','post'],'fdh_mini_dataset_authauto',[App\Http\Controllers\FdhController::class, 'fdh_mini_dataset_authauto'])->name('fdh.fdh_mini_dataset_authauto');
Route::match(['get','post'],'fdh_mini_dataset_pullauto',[App\Http\Controllers\FdhController::class, 'fdh_mini_dataset_pullauto'])->name('fdh.fdh_mini_dataset_pullauto');
Route::match(['get','post'],'fdh_mini_dataset_pullnoinauto',[App\Http\Controllers\FdhController::class, 'fdh_mini_dataset_pullnoinauto'])->name('fdh.fdh_mini_dataset_pullnoinauto'); 
Route::match(['get','post'],'fdh_mini_dataset_apicliamauto',[App\Http\Controllers\FdhController::class, 'fdh_mini_dataset_apicliamauto'])->name('fdh.fdh_mini_dataset_apicliamauto');
Route::match(['get','post'],'fdh_mini_dataset_pulljongauto',[App\Http\Controllers\FdhController::class, 'fdh_mini_dataset_pulljongauto'])->name('fdh.fdh_mini_dataset_pulljongauto');