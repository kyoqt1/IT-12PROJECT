<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});
Route::get('/profile', function () {
    return view('profile');
});
Route::get('/settings', function () {
    return view('settings');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/customers', function () {
    return view('customers');
});

Route::get('/suppliers', function () {
    return view('suppliers'); // This must match the blade filename
});
Route::get('/inventory', function () {
    return view('inventory'); // make sure this file exists: resources/views/inventory.blade.php
});

Route::post('/logout', function () {
    // For now, if using localStorage mock login:
    // You can just redirect to login page
    return redirect('/'); 
})->name('logout');

Route::get('/suppliers/transactions', function() {
    return view('suppliers.transactions');
});
Route::get('/customers/transactions', function() {
    return view('customers.transactions');
});     

Route::get('/suppliers/payments', function() {
    return view('suppliers.payments');
})->name('suppliers.payments');

Route::get('/customers/payments', function () {
    return view('customers.payments');
})->name('customer.payments');