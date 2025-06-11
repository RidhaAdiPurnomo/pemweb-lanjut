<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;

Route::prefix('auth')->group(function () {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me');
});

Route::middleware('auth:api')->group(function () {
    // Dashboard
    Route::get('dashboard', 'DashboardController@index');
    
    // Projects
    Route::apiResource('projects', 'ProjectController');
    
    // Project Tasks
    Route::prefix('projects/{project}/tasks')->group(function () {
        Route::get('/', 'TaskController@index');
        Route::post('/', 'TaskController@store');
    });
    
    // Tasks
    Route::apiResource('tasks', 'TaskController')->except(['index', 'store']);
    
    // Task Comments
    Route::prefix('tasks/{task}/comments')->group(function () {
        Route::get('/', 'CommentController@index');
        Route::post('/', 'CommentController@store');
    });
    
    // Comments
    Route::delete('comments/{comment}', 'CommentController@destroy');
    
    // User Profile
    Route::prefix('profile')->group(function () {
        Route::put('/', function (Request $request) {
            $user = $request->user();
            
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,'.$user->id,
                'password' => 'sometimes|string|min:6|confirmed',
            ]);
            
            $user->update($request->only(['name', 'email']));
            
            if ($request->has('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }
            
            return response()->json($user);
        });
    });
});