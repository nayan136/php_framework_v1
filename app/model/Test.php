<?php


class Test extends Model
{

    private $table = 'auction_item';
    public function __construct()
    {
        parent::__construct();

        $this->setTable($this->table);
    }


}