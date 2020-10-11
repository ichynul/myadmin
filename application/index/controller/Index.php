<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        return '<img src="https://gitee.com/ichynul/myadmin/widgets/widget_card.svg?colors=393222,ebdfc1,fffae5,d8ca9f,393222,a28b40" >';
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
