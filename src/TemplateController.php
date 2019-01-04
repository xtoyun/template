<?
/**
 * ============================================================================
 * * 版权所有 2013-2017 xtoyun.net，并保留所有权利。
 * 网站地址: http://www.xtoyun.net；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: xtoyun $ 
*/
namespace xto\template;


use xto\App;
use xto\membership\context\Context;
use xto\membership\context\Users;
use xto\module\IModule;
use xto\template\Template;
use app\common\dao\ConfigDao;
use think\Controller;
use think\Request;

class TemplateController extends Controller{
	private $app;
	private $context;
    private $template;
    private $module;
    private $m;
    private $c;
    private $a;
    private $cdao;//配置数据读取类

    protected $config; //全局配置
    protected $_vars = [
        'setnav'        =>['isview'=>true],//设置默认值为示
        //'leftblock'     =>['template'=>'','isview'=>false],//isview=false默认状态
        'setoption'     =>['isview'=>true,'settings'=>false,'fullscreen'=>true,'refresh'=>true,'content'=>false,'close'=>true],
        'setlayout'     =>['isview'=>true,'header'=>true,'nav'=>true,'aside'=>true]
    ];

    public function _initialize(){
        parent::_initialize();
        $request=Request::instance();

        $this->cdao=ConfigDao::instance();//读取配置数据类
        $this->app=App::instance(); 
        //$this->template=Template::current($this);//当前模块模板

        $this->m=$request->module();
        $this->c=$request->controller();
        $this->a=$request->action();
        
        $this->module=IModule::current(); //当前模块信息
        $this->context=Context::current();//页面上下文信息
        $this->config=$this->cdao->getConfigs(true);
        
        $this->assign('m',strtolower($this->m));
        $this->assign('c',strtolower($this->c));
        $this->assign('a',strtolower($this->a));

        $this->assign('viewurl',$this->module->viewurl);//当前模块首页 
        $this->assign('layout',$this->layout);//指定母版页面 
        $this->assign('menus',$this->module->menus);//默认模块菜单
        $this->assign('config',$this->config);//配置当前站点信息
        $this->assign('modules',$this->module->modules());//当前所有模块
        
        $this->assign('formlayout',$this->getFormLayout());
        $this->assign('tablelayout',$this->getTableLayout());
        $this->assign('showlayout',$this->getShowLayout());
        $this->assign('theme',$this->getTheme());
    } 

    //重写fetch
    public function fetch($template = '', $vars = [], $replace = [], $config = [])
    {  
        $vars = array_merge($vars, $this->_vars);
        return parent::fetch($template, $vars, $replace, $config); 
    } 

    //重写view
    public function view($template = '', $vars = [], $replace = []){
        $vars = array_merge($vars, $this->_vars);
        return \think\Response::create($template, 'view', 200)->replace($replace)->assign($vars);
    }


    /**
     * 获取主健 
     * @access public
     * @return string
     */
    public function getPid(){ 
        return $this->getData('pid'); 
    }

    /**
     * 设置主键
     * @access public
     * @return string
     */
    public function setPid($idname,$value=null){
        $this->setData('pvalue',$value);
        $this->setData('pid',$idname);
        return $this;
    } 

    /**
     * 获取数据源
     * @access public
     * @return string
     */
    public function getDataSource(){
        return $this->getData('datasource');
    }

    /**
     * 设置数据源
     * @access public
     * @return string
     */
    public function setDataSource($value){
        $this->setData('datasource',$value);
        return $this;
    } 

    /**
     * 获取标题
     * @access public
     * @return string
     */
    public function getTitle(){
        return $this->getData('title');
    }

    /**
     * 设置标题
     * @access public
     * @return string
     */
    public function setTitle($value){
        $this->setData('title',$value);
        return $this;
    } 

    public function setModule($name){
        $this->setData('modulename',$name);
        return $this;
    } 

    /**
     * 获取分页
     * @access public 
     * @return string
     */
    public function getPager(){
        return $this->getData('pager');
    }

    /**
     * 设置分页
     * @access public
     * @return string
     */
    public function setPager($value){
        $this->setData('pager',$value);
        return $this;
    } 

    /**
     * 获取数据项
     * @access public
     * @return string
     */
    public function getData($key){
        if(isset($this->_vars[$key])){
            return $this->_vars[$key];
        }
    }

    /**
     * 设置数据项
     * @access public
     * @return string
     */
    public function setData($key,$value){
        unset($this->_vars[$key]);//移除数组元素
        if(!isset($this->_vars[$key])){
            $this->_vars[$key]=$value;
        }
        return $this;
    } 

    /**
     * 设置数据对象
     * @access public
     * @return string
     */
    public function setDataList($key,$value){
        $this->_vars[$key][]=$value;
        return $this;
    }

    /**
     * layout全新模块控制，
     * @access public
     * @return this
     */
    public function setBlock($name){
        $result=[
            'name'=>$name
        ]; 
        $this->setData('setblock',$result);
        return $this;
    }

    /**
     * layout是否显示头部的导航条，默认显示，
     * @access public
     * @return this
     */
    public function setNav($is_view=true){
        $result=[
            'isview'=>$is_view
        ]; 
        $this->setData('setnav',$result);
        return $this;
    }

    public function setLayout($isview=true,$header=true,$nav=true,$aside=true){
        $result=[
            'isview'    =>$isview,
            '$header'   =>$header,
            '$nav'      =>$nav,
            '$aside'    =>$aside
        ]; 
        $this->setData('setlayout',$result);
        return $this;
    }

    public function addLeftBlock($name,$title,$template){
        $result=[
            'name'=>$name,
            'title'=>$title,
            'template'=>config('template.view_path').$template.'.html',
            'isview'=>true
        ];  
        //外面include总是会调用，未被引用的模板就会报错，用php最原始做法
        $this->setData('leftblock',$result);
        return $this;
    } 


     public function addNavs($columns = [])
    {
        if (!empty($columns)) {
            foreach ($columns as $column) {
                call_user_func_array([$this, 'addNav'], $column);
            }
        }
        return $this;
    }

    public function addNav($type='',$title='',$href='',$paras='',$class='',$vals=[]){
        $result=null;
        switch ($type) {
            case 'index':
                $url=url('index/index');
                $result = [
                    'title' => '首页',
                    'class' => 'active',
                    'href'  => "$url" 
                ];
                break;  
            default:
                $ac='';
                if($href==url($this->c.'/'.$this->a)){
                    $ac='active';
                } 
                $result = [
                    'title' => $title,
                    'class' => (empty($class)?'':$class).$ac,
                    'href'  => $href.$paras
                ];
                # code...
                break;
        }  

        if ($vals && is_array($vals)) { 
            $result=array_merge($result, $vals);
        }   
        $this->setDataList('navs',$result); 
        return $this;
    }

    public function setOption($isview=true,$settings=false,$fullscreen=true,$refresh=true,$content=false,$close=true){

        $result=[
            'isview'    =>$isview,
            'settings'  =>$settings,
            'fullscreen'=>$fullscreen,
            'refresh'   =>$refresh,
            'content'   =>$content,
            'close'     =>$close,
        ]; 
        $this->setData('setoption',$result);
        return $this;
    }

    /**
     * 查找配置信息，例:find('app_name')
     * @access public
     * @return string
     */
    public function find($name){
        return $this->cdao->getconfig($name); 
    }

    /**
     * 默认全局母板页
     * @access public
     * @return string
     */
    public function getlayout(){
        if(isset($this->view->layout)){
            return $this->view->layout;
        }else{
            return APP_PATH."common/view/$this->theme/template/layout.html";
        }  
    }

    /**
     * 默认表单模板页
     * @access public
     * @return string
     */
    public function getFormLayout(){ 
        if(isset($this->view->formlayout)){
            return $this->view->formlayout;
        }else{
            return APP_PATH."common/view/$this->theme/template/form.html";//默认模板
        }         
    }

    /**
     * 默认表格模板页
     * @access public
     * @return string
     */
    public function getTableLayout(){
        if(isset($this->view->tablelayout)){
            return $this->view->tablelayout;
        }else{
            return APP_PATH."common/view/$this->theme/template/table.html";
        }  
    }

    /**
     * 默认表格模板页
     * @access public
     * @return string
     */
    public function getShowLayout(){
        if(isset($this->view->showlayout)){
            return $this->view->showlayout;
        }else{
            return APP_PATH."common/view/$this->theme/template/show.html";
        }  
    }

    /**
     * 全局皮肤请在模块根目录template.php重写getTheme方法即可，不同模块读取不同皮肤
     * 如果只是修改tableTemplate,在此类重写getTemplateLayout方法
     * @access public
     * @return string
     */
    public function getTheme(){
        if(isset($this->view->theme)){
            return $this->view->theme;
        }else{
            return 'xui';//默认模板
        }   
    }

    /**
     * 检查权限
     * @access public
     * @return 直接输入错误提示
     */
    public function checkfun($funid){ 
        if(!Users::checkfun($funid)){
            $this->error("权限不够");
            return;
        }
    } 

    /**
     * 获取当前模板类
     * @access public
     * @return Template
     */
    public function getTemplate(){
        //return $this->template;
        return $this;
    }

    public function getFormTemplate(){
        $m=Request()->module();
        $url=str_replace("/", "\\", "/app/$m/template/FormTemplate");
        $r = new \ReflectionClass($url);
        if($r->implementsInterface('\xto\template\iTemplate')){
            return new $url($this);
        }
        //return $this;
    }

    public function getTableTemplate(){
        $m=Request()->module();
        $url=str_replace("/", "\\", "/app/$m/template/TableTemplate");
        $r = new \ReflectionClass($url);
        if($r->implementsInterface('\xto\template\iTemplate')){
            return $this->template=new $url($this);
        }
    } 

    public function getShowTemplate(){
        $m=Request()->module();
        $url=str_replace("/", "\\", "/app/$m/template/ShowTemplate");
        $r = new \ReflectionClass($url);
        if($r->implementsInterface('\xto\template\iTemplate')){
            return $this->template=new $url($this);
        }
    }

    /**
     * 获取当前模块类
     * @access public
     * @return Module
     */
    public function getModule(){
        return $this->module;
    }

    /**
     * 获取模块配置类
     * @access public
     * @return Module
     */
    public function getConfig(){
        return $this->module->config;
    }

    /**
     * 获取全局类
     * @access public
     * @return Module
     */
	public function getApp(){
		return $this->app;
	} 

    public function getAction(){
        return request()->action();
    }

    public function getController(){
        return request()->controller();
    }

    /**
     * 获取用户上下文信息，指登录后用户名，用户等
     * @access public
     * @return Module
     */
	public function getContext(){
		return $this->context;
	}

	public function __get($name)              // 这里$name是属性名
    {
        $getter = 'get' . $name;              // getter函数的函数名
        if (method_exists($this, $getter)) {
            return $this->$getter();          // 调用了getter函数
        } else {
            if(isset($this->$name)){
                return $this->$name; 
            } 
        }
    }

    public function __set($name, $value)
    {
        $setter = 'set' . $name;             // setter函数的函数名
        if (method_exists($this, $setter)) {
            $this->$setter($value);          // 调用setter函数
        } else { 
            $this->$name = $value; 
        }
    }
}