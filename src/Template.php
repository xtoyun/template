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

use think\Request;

abstract class Template extends TemplateController implements ITemplate{
    
	private $container;
	// private $_vars = [
 //        'setnav'        =>['isview'=>true],//设置默认值为示
 //        //'leftblock'     =>['template'=>'','isview'=>false],//isview=false默认状态
 //        //'setoption'     =>['settings'=>false,'fullscreen'=>true,'refresh'=>true,'content'=>false,'close'=>true]
 //    ];

    /**
     * 初始当前模块
     * @access public
     * @return string 
     */
    static function current($page){ 
        $url='\\app\\'.Request::instance()->module().'\\Template';
        if(class_exists($url)){
            $result= new $url($page);
            return $result;
        }
        
    }

    static function instance(){ 
        if (is_null ( self::$instance ) || isset ( self::$instance )) {
            self::$instance = new static ();  
        }
        return self::$instance;
    }

    /**
     * 获取Template的layout，必须要定义
     * @access public
     * @return string
     */
    abstract function getTemplateLayout();
    /**
     * 输出标签的输入函数
     * @access public
     * @return string
     */
    abstract function render();


    /**
     * 获取View的模板，可以外面重写来定义路径，默认是从container里面获取
     * @access public
     * @return string
     */
    public function getViewLayout(){
        return $this->container->ViewLayout;
    }

    public function getModule(){ 
        return $this->container->module;
    }

    public function getAction(){
        return $this->container->action;
    }

    public function getController(){
        return $this->container->controller;
    } 
    public function __construct($tc){
        parent::__construct();   
        $this->container=$tc;  
        $this->setData('module',$this->module);
        $this->setData('action',$this->action);
        $this->setData('controller',$this->controller); 
    }

    //模板要根据实际情况重写
	public function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        $this->render();
    	if (empty($template)) {
    		$template=$this->getTemplateLayout();
    	}
        $vars = array_merge($vars, $this->_vars);
        //dump($template);
        //return parent::fetch($template, $vars, $replace, $config); 
        return $this->container->fetch($template, $vars, $replace, $config);
    } 

    
    //模板要根据实际情况重写
    public function view($template = '', $vars = [], $replace = []){
        $this->fetch($template,$vars,$replace);
    }

    public function __get($name)              // 这里$name是属性名
    {
        $getter = 'get' . $name;              // getter函数的函数名
        if (method_exists($this, $getter)) {
            return $this->$getter();          // 调用了getter函数
        } else {
            return $this->$name;
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