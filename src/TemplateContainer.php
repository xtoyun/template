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

abstract class TemplateContainer{

    public function __construct($page){
        $this->page=$page;
    }
    public $page;
    /**
     * 虚拟路径
     * @access public
     * @return string
     */
	abstract function getTemplateVirtualPath();

    /**
     * View层layout
     * @access public
     * @return string
     */
    //public function getViewLayout(){
    //    return $this->root."/application/common/view/$this->theme/admin/layout.html";
    //}

    public function getTheme(){
        return 'xui';
    }

    public function getRoot(){
        return str_replace('\\','/',realpath(dirname(__FILE__).'/../../'));
    }

	/**
     * 获取表格模板
     * @access public
     * @return string
     */
	public function getTableTemplate(){
		$url=str_replace('/','\\',$this->TemplateVirtualPath.'/TableTemplate');
		$r = new \ReflectionClass($url);
        if($r->implementsInterface('\xto\template\iTemplate')){
        	return new $url($this);
        }
        return null;
	} 

	/**
     * 获取表单模板
     * @access public
     * @return string
     */
	public function getFormTemplate(){
		$url=str_replace('/','\\',$this->TemplateVirtualPath.'/FormTemplate');
		$r = new \ReflectionClass($url);
        if($r->implementsInterface('\xto\template\iTemplate')){
        	return new $url($this);
        }
        return null;
	}

    /**
     * 获取显示模板
     * @access public
     * @return string
     */
    public function getShowTemplate(){
        $url=str_replace('/','\\',$this->TemplateVirtualPath.'/ShowTemplate');
        $r = new \ReflectionClass($url);
        if($r->implementsInterface('\xto\template\iTemplate')){
            return new $url($this);
        }
        return null;
    }

    public function getModule(){ 
        return request()->module();
    }

    public function getAction(){
        return request()->action();
    }

    public function getController(){
        return request()->controller();
    }

    /**
     * 返回属性
     * @access public
     * @return array
     */
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

    /**
     * 设置属性
     * @access public
     * @return array
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;             // setter函数的函数名
        if (method_exists($this, $setter)) {
            $this->$setter($value);          // 调用setter函数
        } else {
             if(isset($this->$name)){
                $this->$name = $value; 
            }
        }
    }
}