<?php

// ? -> required if method has parameter
Router::get('/','HomeController');
Router::post('/post','HomeController@post');//post request
Router::get('/admin/test/?','admin/HomeController');

