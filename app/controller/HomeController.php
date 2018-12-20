<?php

class HomeController extends Controller
{
    public function index()
    {
        $test = new Test();
        $user = new User();
        //dnd($user->insert(["Nayanjyoti Sharma","test@email.com","9085789980"]));
        //dnd($user->select()->where("id",2)->where("name","Nayanjyoti Sharma")->get());
        //$sql = "SELECT * FROM user WHERE id = ? AND name = ?";
        //dnd($user->raw($sql,array(2,"Nayanjyoti Sharma"))->get());
        $result = $user->select()->innerJoin('auction_item','item_id','auction_id')->get();
//        dnd($test->select()->get());
//        dnd($user->select()->get());
        View::render('home',compact("result"),"Home");
    }

    public function post()
    {
        echo "Hello World";

    }
}