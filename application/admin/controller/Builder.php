<?php
namespace app\admin\controller;

use think\Controller;
use tpext\builder\common\Builder as Bd;

class Builder extends Controller
{
    public function sumary()
    {
        $builder = Bd::getInstance('Builder', '总览');

        $form = $builder->form();

        $demo1 = <<<EOT
```php
<?php
namespace app\admin\controller;

/**引入Builder**/
use tpext\builder\common\Builder；
use think\Controller;

class Demo extends Controller
{
    public function demo1()
    {
        \$builder = Builder::getInstance('标题', '描述');
        \$form = \$builder->form(); //获取一个表单 实例
        \$form->text('name', '姓名')->required();
        \$form->number('age', '年龄')->min(18)->required();
        \$form->radio('gender', '性别')->options([1 => '男', 2 => '女'])->default(1);
        \$form->switchBtn('status', '状态');
        \$form->textarea('about', '简介')->maxlength(500);

        //设置提交地址，同控制器的action:demo1Post()，不填则提交到相同action:demo1(),可根据是否为为ajax判断展示数据还是保存数据
        \$form->action(url('demo1Post'));

        //数据填充，一般为数据库查询的一条记录
        \$data = ['name' => '', 'age' => 6, 'gender' => 1, 'status' => 1, 'about' => '我叫小明'];

        \$form->fill(\$data);

        return \$builder->render();
    }
｝
```

EOT;

        $form->mdreader('表格[Form]')->value($demo1)->size(12, 12); //markdown 展示
        $form->raw('demo1', ' ')->showLabel(false)->value('<a onclick="top.$.fn.multitabs().create(this, true);return false;" href="' . url('demo/demo1') . '">点此查看</a>表单实例');

        $demo2 = <<<EOT
```php
<?php
namespace app\admin\controller;

use tpext\builder\common\Builder；
use think\Controller;

class Demo extends Controller
{
    public function demo2()
    {
        \$builder = Builder::getInstance('builer', 'table');
        \$table = \$builder->table(); //获取一个表格 实例
        \$table->text('name', '姓名')->autoPost()->getWrapper()->addStyle('width:140px;');//限制列宽度
        \$table->show('age', '年龄');
        \$table->match('gender', '性别')->options([1 => '男', 2 => '女'])->mapClassWhen(1, 'success')->mapClassWhen(2, 'info');
        \$table->switchBtn('status', '状态')->autoPost();
        \$table->show('about', '简介')->cut(50)->getWrapper()->addStyle('width:30%;');

        \$data = \$this->getData();

        \$table->fill(\$data);
        \$table->paginator(count(\$data), 12);

        if (request()->isAjax()) {
            return \$table->partial()->render();
        }

        return \$builder->render();
    }

    /**
     * 生成模拟数据
     *
     * @return array
     */
    private function getData()
    {
        //数据填充，一般为数据库查询的多条记录
        \$data = [];

        \$names = ['小明', '小红', '小刚', '小芳'];
        for (\$i = 0; \$i < 12; \$i += 1) {
            \$name = \$names[mt_rand(0, count(\$names) - 1)];
            \$data[] = ['name' => \$name, 'age' => mt_rand(15, 25), 'gender' => mt_rand(1, 2), 'status' => 1, 'about' => str_repeat('我叫' . \$name, mt_rand(3, 30))];
        }

        return \$data;
    }
｝
```

EOT;
        $form->mdreader('表格[Table]')->value($demo2)->size(12, 12);
        $form->raw('demo2', ' ')->showLabel(false)->value('<a onclick="top.$.fn.multitabs().create(this, true);return false;" href="' . url('demo/demo2') . '">点此查看</a>表格实例');

        $form->bottomButtons(false); //仅显示，不需要表单提交按钮

        return $builder->render();
    }

    public function useContent()
    {
        $demo3 = <<<EOT
```php
<?php
namespace app\admin\controller;

use tpext\builder\common\Builder；
use think\Controller;

class Demo extends Controller
{
    public function demo2()
    {
        \$builder = Builder::getInstance('builer', 'content');

        \$builder->row()->content(6)->display('<div style="widht:100%;height:30px;margin-top:10px;border:1px dashed red;">col-md-6</div>');
        //新的一个row,所以这个col-md-6不会跟随上面一个
        \$builder->row()->content(6)->fetch(); //模板路径规则跟tp框架一致。留空模板名就是当前action`admin/view/demo/demo3.html`
        //新的一个row,所以这个col-md-6不会跟随上面一个
        \$builder->row();

        // \$builder->column(6)->content() 跟 \$builder->content(6)等效。table,form也类似
        \$builder->content(6)->display('<div style="widht:100%;height:30px;margin:20px 0;border:1px dashed red;">{\$name}</div>', ['name' => 'col-md-6']);
        \$builder->column(4)->content()->fetch('demo33', ['name' => 'col-md-4']); //另外指定模板`admin/view/demo/demo33.html`

        \$builder->row();

        //树形结构，col-md-n 控制宽带在这里是不灵活的，让它变成`col-md-0 left-tree`。然后给`left-tree`定义宽度。
        \$tree = \$builder->tree('0 left-tree');
        \$table = \$builder->table('0 right-list');

        \$sarch = \$table->getSearch();

        \$treeData = \$this->getTree();
        \$tree->fill(\$treeData);

        \$sarch->select('category_id', '分类')->optionsData(\$treeData); //这个搜索字段用来接收树形结构被点击的值。
        \$sarch->text('name', '姓名');

        \$tree->trigger('.row-category_id'); //绑定触发

        //
        \$data = \$this->getData();
        \$table->fill(\$data);

        \$builder->addStyleSheet('
            .left-tree
            {
                width:12%;
                float:left;
            }

            .right-list
            {
                width:88%;
                float:right;
            }
        ');

        if (request()->isAjax()) {

            //表格ajax的时候，只会替换table部分，若需要实时更新的数据，可使用addTop\addBottom。
            \$table->addTop()->content()->display('<label class="text-center label label-default">搜索:' . json_encode(input('post.')) . '</label>');

            \$table->addBottom()->content()->display('<script>layer.msg("搜索:category_id-' . input('post.category_id') . '")</script>');

            return \$table->partial()->render();
        }

        return \$builder->render();
    }

    /**
     * 生成模拟数据
     *
     * @return array
     */
    private function getData()
    {
        //数据填充，一般为数据库查询的多条记录
        \$data = [];

        \$names = ['小明', '小红', '小刚', '小芳'];
        for (\$i = 0; \$i < 12; \$i += 1) {
            \$name = \$names[mt_rand(0, count(\$names) - 1)];
            \$data[] = ['name' => \$name, 'age' => mt_rand(15, 25), 'gender' => mt_rand(1, 2), 'status' => 1, 'about' => str_repeat('我叫' . \$name, mt_rand(3, 30))];
        }

        return \$data;
    }

    /**
     * 生成模拟树形数据
     *
     * @return array
     */
    private function getTree()
    {
        return [
            ['name' => '10~12', 'id' => 1, 'parent_id' => 0],
            ['name' => '12~16', 'id' => 2, 'parent_id' => 0],
            ['name' => '16~20', 'id' => 3, 'parent_id' => 0],
            ['name' => '20~22', 'id' => 4, 'parent_id' => 0],
            ['name' => '22~24', 'id' => 5, 'parent_id' => 0],
            //
            ['name' => '16', 'id' => 6, 'parent_id' => 3],
            ['name' => '17', 'id' => 7, 'parent_id' => 3],
            ['name' => '18', 'id' => 8, 'parent_id' => 3],
            ['name' => '19', 'id' => 9, 'parent_id' => 3],
            ['name' => '20', 'id' => 10, 'parent_id' => 3],
            //
            ['name' => '22', 'id' => 11, 'parent_id' => 5],
            ['name' => '23', 'id' => 12, 'parent_id' => 5],
        ];
    }
｝
```
EOT;

        $builder = Bd::getInstance('Builder', '总览');

        $form = $builder->form();

        $form->mdreader('自由布局，渲染模板，使用树行结构')->value($demo3)->size(12, 12); //markdown 展示
        $form->raw('demo1', ' ')->showLabel(false)->value('<a onclick="top.$.fn.multitabs().create(this, true);return false;" href="' . url('demo/demo3') . '">点此查看</a>实例');

        $form->mdreader('demo3html', 'admin/view/demo/demo3.html')->size(12, 12)->value('`<div style="widht:100%;height:30px;margin-top:10px;border:1px dashed green;">col-md-6</div>`');
        $form->mdreader('demo33html', 'admin/view/demo/demo33.html')->size(12, 12)->value('`<div style="widht:100%;height:30px;margin:20px 0;border:1px dashed green;">{$name}</div>`');

        $form->bottomButtons(false); //仅显示，不需要表单提交按钮

        return $builder->render();
    }

    public function useDisplay()
    {

    }

    public function useFetch()
    {

    }
}
