<?php

class HomeController extends Controller
{
    public function index()
    {
        View::render('home',[1,2],"Home");
    }
}