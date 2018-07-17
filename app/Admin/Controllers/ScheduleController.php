<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/17
 * Time: 10:51
 */
namespace App\Admin\Controllers;
use App\Admin\Extensions\ExcelExporter;
use App\Admin\Extensions\Tools\SynTask;
use App\Handlers\RedisHandler;
use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\InfoBox;
use Illuminate\Support\Facades\DB;
class ScheduleController extends Controller
{
    use ModelForm;
    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('number管理');
            $content->description('系统每五分钟计算一次');
            $content->row(function ($row) {
                $row->column(6, new InfoBox('总成功个数', '', 'green', action('\App\Admin\Controllers\ScheduleController@index', []),
                    DB::table("schedule")->sum('success_amount')));
                $row->column(6, new InfoBox('总失败个数', '', 'red', action('\App\Admin\Controllers\ScheduleController@index', []),
                    DB::table("schedule")->sum('failed_amount')));
            });
            $content->body($this->grid());
        });
    }



    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('number管理');
            $content->body($this->form('edit')->edit($id));
        });
    }



    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header('number管理');
            $content->description('');
            $content->body($this->form());
        });

    }



    /**
     * Make a grid builder.
     *
     * @return Grid
     */

    protected function grid()
    {
        return Admin::grid(Schedule::class, function (Grid $grid) {
            if (!request()->get('_sort')) $grid->model()->orderByDesc('id');
            $grid->id('ID')->sortable();
            $grid->column('today_num', '当日个数')->sortable();
            $grid->column('success_amount', '当日成功数')->sortable();
            $grid->column('failed_amount', '当日失败数')->sortable();
            $grid->column('created_at', '创建时间')->sortable();
            $grid->disableActions();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('success_amount', '当日成功数');;
            });
            $grid->exporter(new ExcelExporter());
            $grid->tools(function ($tool) {
                $tool->append(new SynTask());
            });
        });

    }

    public function sync()
    {
        RedisHandler::SyncToday();
        return ['status_code' => 200, 'message' => '同步成功'];
    }
}

