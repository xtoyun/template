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

use think\Cache;
use think\Request;
use app\data\membership\Users;
use xto\membership\core\UserHelper;
use app\data\model\Config;
use app\data\App;
use think\facade\Session; 

abstract class IModule{
	private $data=[];
	private $version;
	private $config;	
	private static $instance;
	private $modulename='';

	/**
     * 首页显示地址
     * @access public
     * @return string
     */
	abstract function getViewUrl();

	/**
     * 必须实现安装
     * @return mixed
     */
    abstract function install();

    /**
     * 必须卸载插件方法
     * @return mixed
     */
    abstract function uninstall();

	/**
     * 菜单地址
     * @access public
     * @return string
     */
	abstract function modulePath();

	/**
     * 模块名称
     * @access public
     * @return string
     */
	abstract function getName(); 
	/**
     * 配置菜单
     * @access public
     * @return string
     */
	protected function configPath(){
		return $this->modulePath().'/config.xml';
	}

	/**
     * 管理员菜单
     * @access public
     * @return string
     */
	protected function menuPath(){
		return $this->modulePath().'/admin.xml';
	}

	public function getModulename(){
		return $this->modulename;
	}

	public function getIsSys(){
		return false;
	}

	public function getIsShow(){
		return false;
	}

	public function getConfig($action){ 
		if(file_exists($this->configPath())){
			$c=file_get_contents($this->configPath()); 

			$result=XML2Array::createArray($c);

			$items=$result['config']['item'];

			//$cdao=ConfigDao::instance();
			//$configs=$cdao->getConfigs(false);//从数据库读取数据

			$data=[];
			$navs=[];//导航条

			foreach ($items as $item) {
				$tab=$item['@attributes']['tab'];
				if(!in_array($tab,$navs)){
					$navs[$tab]=['',$item['@attributes']['group'],url($tab)];
				} 
			}//读取选项卡

			foreach ($items as $item) { 
				$tab=$item['@attributes']['tab'];
				$name=$item['@attributes']['name'];
				if($action==$tab){
					$t=Util::strToArray('|',$item['@attributes']['attr']);
					$data[]=[$item['@attributes']['type'],
						$item['@attributes']['name'],
						$item['@attributes']['title'],
						$item['@attributes']['tips'],
						//isset($configs[$name])?$configs[$name]:'',
						'',
						$t]; 
				}
			}
	        return [
	        	'data'=>$data,
	        	'navs'=>$navs
	        ];
	    }
	}

	public function s(){
		$s=[];
		$navs=[];//导航条

		$items=$this->module->config['config']['item'];
		foreach ($items as $item) {
			$tab=$item['@attributes']['tab'];
			if(!in_array($tab,$navs)){
				$navs[$tab]=['',$item['@attributes']['group'],url($tab)];
			} 
		}//读取选项卡
		foreach ($items as $item) { 
			$tab=$item['@attributes']['tab'];
			if($action==$tab){
				$t=\xto\Util::strToArray('|',$item['@attributes']['attr']);
				$s[]=[$item['@attributes']['type'],
					$item['@attributes']['name'],
					$item['@attributes']['title'],
					$item['@attributes']['tips'],
					$item['@value'],
					$t]; 
				}
		}
	}

	public static function instance(){ 
		if (is_null ( self::$instance ) || isset ( self::$instance )) {
            self::$instance = new static();    
        }
        return self::$instance;
	} 

	static function current(){
		$request=request();
		$url='\\app\\'.$request->module().'\\Module';
		return $url::instance();
	}

	public function getMenus(){
		$this->loadmenu(); 
		if(isset($this->data['menu'])){
			return $this->data['menu'];
		}
		
		// $c=Cache::get($this->cachename());
		// if (!empty($c)) {
		// 	return $c;
		// }else{
		// 	$this->loadmenu();
		// 	Cache::set($this->cachename(),$menus,3600);
		// 		if(isset($this->data['menu'])){
		// 		return $this->data['menu'];
		// 	}
		// }  
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
            if(isset($this->$name)){
                $this->$name = $value; 
            } 
        }
    }  

	//读取菜单处理
	final private function loadmenu(){
		// dump(Session::get(App::user_auth()));
		// dump(App::get_manager_username());
		$user=Users::getuser(0,App::get_manager_username());//获取当前管理员
		// dump($user);
		// die;
		if(is_null($user)){
			$this->data['menu']=null;
			return $this;
		}
		//$funs=UserHelper::getUserFunctions($user->userid);

		if(file_exists($this->menuPath())){

			$xml=simplexml_load_file($this->menuPath());

			$doc= $xml[0];//所有菜单
			$c=strtolower(request()->controller());

			$menus=[]; 
			foreach ($doc as $key => $value) {
				$status=false;//控制当前权限
				//如果是管理员，直接通过
				if($user->is_admin){
					$status=true;
				}else if(!$user->is_admin && in_array((string)$value['url'],$funs)){
					//检查是否有权限访问当地址
					$status=true;
				}
				if($status){
					$ps=array();
					$isclass=false;
					foreach ($value->pagelink as $key1 => $value1) {
						$status=false;
						if($user->is_admin){
							$status=true;
						}else if(!$user->is_admin && in_array((string)$value1['url'],$funs)){
							$status=true;
						}
						if($status){
							$value_c=(string)$value1['c'];
							if(strpos($value_c,$c)!==false){
								$isclass=true;
							}
							$fu=array();
							foreach ($value1->function as $key2 => $value2) {
								$fu[]=[
									'title'=>(string)$value2['title'],
									'url'=>(string)$value2['url']
									];
							}
							$ps[]=[
								'title'=>(string)$value1['title'],
								'class'=>(string)$value1['class'],
								'current'=>strpos($value_c,$c)!==false?'active':'',
								'url'=>(string)$value1['url'],
								'link'=>url((string)$value1['url']),
								'function'=>$fu
							];
						}
					} 
					$menus[]=[ 
					'title'=>(string)$value['title'],
					'class'=>(string)$value['class'],
					'url'=>(string)$value['url'],
					'link'=>url((string)$value['url']),
					'current'=>$isclass?'active':'',
					'pagelink'=>$ps]; 
				}
			}

		}
		if (!empty($menus)) {
			$this->data['menu']=$menus; 
		}
		
		return $this;
	}

	private function cachename(){
		return 'module-'.$this->name;
	}

	public function modules($all=null){
		$mod=array();
		$dir=APP_PATH;
		$handler = opendir(APP_PATH);
		while( ($filename = readdir($handler)) !== false ) {
		     if($filename != "." && $filename != ".." && is_dir($dir.$filename)){
		     	if(file_exists($dir.$filename.'/Module.php')){
		     		$url="\\app\\$filename\\Module";
		        	$t=$url::instance();
		        	$t->modulename=$filename;
		        	if(is_null($all)){
		        		if($t->issys){
			        		$mod[$filename]=$t;
			        	}
			        	else if(!$t->issys && $t->isshow){
			        		$mod[$filename]=$t;
			        	}
		        	}else{
		        		$mod[$filename]=$t;
		        	}
		     	}
		    }
		}
		return $mod;
	}
}