<?php

class HomeController extends Controller
{
    public function index($id)
    {
        dnd($id);
        //dnd($name);
        View::render('admin.admin',[1,2],"Home");
    }
}