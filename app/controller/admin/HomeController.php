<?php

class HomeController extends Controller
{
    public function index($id)
    {
        View::render('admin.admin',[1,2],"Home");
    }
}