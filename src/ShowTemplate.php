<?php
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
use xto\Util;

abstract class ShowTemplate  extends Template{

    /**
     * 默认路径
     * @access public
     * @return string
     */
	public function getTemplateLayout(){
        if(isset($this->view->showlayout)){
            return $this->view->showlayout;
        }  
	}

    public function render(){
        $this->setDataSource($this->datasource);
    } 

	public function setColumn($name = '', $title = '', $type = '', $default = '', $param = '', $class = ''){
	 	$column = [
            'name'    => $name,
            'title'   => $title,
            'type'    => $type,
            'default' => $default,
            'param'   => $param,
            'class'   => $class
        ];

        $args   = array_slice(func_get_args(), 6);//生成数组为6
        $column = array_merge($column, $args);
        $this->setDataList('columns',$column);
        return $this;
	}

	public function setColumns($columns = [])
    {
        if (!empty($columns)) {
            foreach ($columns as $column) {
                call_user_func_array([$this, 'setColumn'], $column);
            }
        }
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
                if($href==url($this->controller.'/'.$this->action)){
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

    public function setQuickSearch($field = '',$default='',$title='',$class='', $vals = []){
        $result = [
                    'field'     => $field,
                    'title'     => $title,
                    'default'   => $default,
                    'class'     => (empty($class)?'':$class),
                ];
        if ($vals && is_array($vals)) {
            $result = array_merge($result, $vals);
        }  
        $this->setData('quicksearch',$result);
        return $this;
    }

    public function addTopButton($type = '',$title='',$href='',$class='', $vals = []){
        $result=null;
        switch ($type) {
            case 'create':
                $key=url($this->controller.'/create');
                $result = [
                    'title' => '创建',
                    'class' => 'btn btn-primary',
                    'href'  => "$key" 
                ];
                break;  
            default:
                $result = [
                    'title' => $title,
                    'class' => empty($class)?'btn btn-primary':$class,
                    'href'  => $href
                ];
                break;
        }

        if ($vals && is_array($vals)) {
            $result = array_merge($result, $vals);
        } 
        $this->setDataList('topbtn',$result);
        return $this;
    }

    public function addColumnButton($type = '',$title='',$href='',$class='',$icon='', $vals = []){
        switch ($type) {
            case 'edit':
                //$key=url($this->controller.'/detail');
                $key=strtolower($this->controller.'_'.$this->action.'_edit');
                $result = [
                    'title' => '修改',
                    'icon'  => 'fa fa-pencil',
                    'class' => 'btn btn-xs btn-default ajax-get confirm',
                    //'href'  => "$key?id=#id#"
                    'href'  => "javascript:app.url(#id#,'$key')"
                ];
                break;
            case 'delete':
                $key=strtolower($this->controller.'_'.$this->action.'_delete');
                $result = [
                    'title' => '删除',
                    'icon'  => 'fa fa-times',
                    'class' => 'btn btn-xs btn-default ajax-get confirm',
                    'href'  => "javascript:app.delete(#id#,'$key')"
                ];
                break; 
            default: 
                $result = [
                    'title' => $title,
                    'icon'  => empty($icon)?'fa fa-times':$icon,
                    'class' => empty($class)?'btn btn-xs btn-default ajax-get confirm':$class,
                    'href'  => $href
                ];
                break;
        }

        if ($vals && is_array($vals)) {
            $result = array_merge($result, $vals);
        } 
        $this->setDataList('columnbtn',$result);
        return $this;
    }
}