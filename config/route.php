<?php

// ? -> required if method has parameter

Router::set('/','HomeController');
Router::set('/admin/test/?','admin/HomeController');