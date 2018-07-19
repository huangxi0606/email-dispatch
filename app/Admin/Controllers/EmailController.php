<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ExcelExporter;
use App\Admin\Extensions\Tools\ImportButton;
use App\Admin\Extensions\Tools\TrashButton;
use App\Admin\Extensions\Tools\SynTask;
use App\Handlers\RedisHandler;
use App\Models\Email;
use Carbon\Carbon;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Table;
use Illuminate\Support\Facades\DB;
use League\Csv\Exception;
use League\Csv\Reader;
class EmailController extends Controller

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

            $content->header('邮箱管理');
            $content->description('');
            $content->row(view('admin.ImportPopup'));
            $content->row(view('admin.trashButton'));
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

            $content->header('邮箱管理');
            $content->description('');
            $content->body($this->form()->edit($id));
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
            $content->header('邮箱管理');
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
        return Admin::grid(Email::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->column('email', 'email');
            $grid->column('pwd', 'pwd');
            $grid->column('status', '状态')->sortable()->display(function ($value) {
                $statusColor = config('system.email.status_color')[$value];
                return "<span class=\"label label-$statusColor\">" . config('system.email.status')[$value] . "</span>";
            });
            $grid->column('reason','reason')->display(function($value){
                if($value) $value=substr($value,0,100);
                return <<<EOF
              <span class="lineChartRender">$value</span>
EOF;
            });
            $grid->column('question','问题')->display(function($value){
                $data =unserialize($value) ;
                if($data) $value = implode(',',$data);
                return <<<EOF
              <span class="lineChartRender">$value</span>
EOF;
            });
            $grid->column('answer','答案')->display(function($value){
                $data =unserialize($value) ;
                if($data) $value = implode(',',$data);
                return <<<EOF
              <span class="lineChartRender">$value</span>
EOF;
            });
            $grid->created_at('创建时间')->sortable();
            $grid->updated_at('更新时间')->sortable();
            $grid->column('详情')->expand(function () {
                return new Table([], [
                    'account'=>$this->account,
                    'pwd' => $this->pwd,
                    'countryCode' => $this->countryCode,
                    'lastName' => $this->lastName,
                    'firstName' => $this->firstName,
                    'birthYear' => $this->birthYear,
                    'birthMonth' => $this->birthMonth,
                    'birthDay' => $this->birthDay,
                    'billingLastName'=>$this->billingLastName,
                    'billingFirstName'=>$this->billingFirstName,
                    'addressOfficialLineFirst'=>$this->addressOfficialLineFirst,
                    'addressOfficialPostalCode'=>$this->addressOfficialPostalCode,
                    'addressOfficialCity'=>$this->addressOfficialCity,
                    'addressOfficialStateProvince'=>$this->addressOfficialStateProvince,
                    'addressOfficialCountryCode'=>$this->addressOfficialCountryCode,
                    'phoneOfficeNumber'=>$this->phoneOfficeNumber,
                    'paymentMethodType'=>$this->paymentMethodType,
                    'reason'=>$this->reason,
                    'proxy' => $this->proxy,

                ]);
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('email', 'email');
            });
            $grid->tools(function ($tool) {
                $importButton = <<<EOF
        <a href="javascript:initLayer()" class="btn btn-sm btn-info">
        <i class="fa fa-cloud"></i>&nbsp;&nbsp;导入
        </a>
EOF;
                $tool->append($importButton);
                $tool->append(new SynTask());
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Email::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('email', 'email');
            $form->display('created_at', '创建时间');
        });

    }

    public function sync()
    {
        RedisHandler::syncEmail();
        return ['status_code' => 200, 'message' => '同步成功'];
    }

    public function trash(Email $email)
    {
        $redis = app('redis');
        $keys =$redis->keys("list:email:*");
        if($keys){
            foreach ($keys as $key){
                $redis->del($key);
            }
        }
        $items =[];
        $hashs =$redis->keys("email:*");
        if($hashs){
            foreach ($hashs as $hash){
                array_push($items,$hash);
            }
        }
        $chunks = array_chunk($items, 2000);
        $redis->pipeline(function ($pipe) use ($items){
            $pipe->del($items);
        });

        $redis -> set("maxacid",0);
        \DB::table($email->getTable())->truncate();
        return ['status_code' => 200, 'message' => '删除全部邮箱成功'];
    }

    public function import()
    {
        ini_set('memory_limit','2048M');
        ini_set('max_execution_time',3600);
        $request = \request();
        $validFields = config('system.email.export_fields');
        $file = $request->file('file');
        $items = [];
        if (!$file->isValid()) {
            return ['status_code' => 10001, 'message' => '上传失败'];
        }
        if (!in_array($file->getMimeType(), ['text/plain'])) {
            return ['status_code' => 10002, 'message' => '请上传csv文件'];
        }
        try{
            $csv = Reader::createFromPath($file->getRealPath(), 'r')->setHeaderOffset(0);
        }catch (Exception $e){
            return ['status_code' => 10003, 'message' => '读取文件失败'];
        }
        foreach ($csv as $data){
            $data = array_intersect_key($data, $validFields);
            $data['created_at'] =Carbon::now();
            array_push($items,$data);
        }
        $chunks = array_chunk($items, 2000);
        foreach ($chunks as $chunk) {
            DB::table('email')->insert($chunk);
        }
        $added_amount = count($items);
        return ['status_code' => 200, 'message' => "新增{$added_amount}条"];
    }
}

