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

abstract class TableTemplate  extends Template{

    /**
     * 默认路径
     * @access public
     * @return string
     */
	public function getTemplateLayout(){
        if(isset($this->view->tablelayout)){
            return $this->view->tablelayout;
        }  
	}

    public function render(){ 
        //合并数据源
        $new_source=[];
        $source=$this->datasource;
        if(!is_null($source)){
            $columns=$this->getData('columns'); //获取所有列

            foreach ($source as $key => &$row) {
                if (!isset($row['button'])) {
                    $row['button'] = '';
                } 
                $id=isset($row[$this->pid])?$row[$this->pid]:0;
                if (!empty($this->getData('columnbtn'))) {
                    foreach ($this->getData('columnbtn') as $index => $btn) { 
                        $p_class=$btn['class']; 
                        $p_icon=$btn['icon'];
                        $p_title=$btn['title']; 
                        $p_vals=$btn['vals'];

                        $columnbtn_is_view=true;
                        if (!empty($p_vals)) {
                            try {
                                $str='$columnbtn_is_view = ('.$this->getDefault($row,$columns,$p_vals).');';
                                eval($str);  
                            } catch (Exception $e) {
                                
                            } 
                        }
                        
                        if($columnbtn_is_view){
                            $p_href=str_replace('#id#', $id, $btn['href']); 
                            $p_href=str_replace('$'.$this->pid, $id, $p_href); 

                            if (!empty($btn['icon'])) {
                                $row['button'] .="<a href=\"$p_href\"><div class='$p_class'><i class='$p_icon'></i>$p_title</div></a>";
                            }else{
                                $row['button'] .="<a href=''><div class=''></div></a>";
                            } 
                        }
                        
                    } 
                } 
                foreach ($columns as $column) {
                    $name=$column['name'];
                    $default=$column['default'];
                    $param=$column['param'];
                    //if(isset($row[$name])){
                        $v          = isset($row[$name])?$row[$name]:'';
                        $id         = $row[$this->pid];
                        if(empty($default)){
                            $default=$v;
                        }
                        $default=$this->getDefault($row,$columns,$default);

                        switch ($column['type']) {
                            case 'img':
                                $row[$name]="<img src='$v'>";
                                break; 
                            case 'link':
                                $row[$name]="<a href='$default' target='_blank'>$v</a>";
                                break; 
                            case 'switch':
                                $s=($v=='true')?true:false;
                                $checked=$s==true?'checked':'';

                                $row[$name]="<label class='css-input switch switch-sm switch-primary push-10-t'>
                                <input type='checkbox' class='switch_post' name='$id' id='$id' $checked default='$default' param='$param'><span></span>
                                </label>";
                                break; 
                            case 'bool': // 是/否
                                    switch ($v) {
                                        case '0': // 否
                                            $row[$name] = '<i class="fa fa-ban text-danger"></i>';
                                            break;
                                        case '1': // 是
                                            $row[$name] = '<i class="fa fa-check text-success"></i>';
                                            break;
                                    }
                                    break; 
                            default: 
                                $row[$name]=$default;
                                break;
                        }
                    //}
                }  
                $new_source[$key]=$row;
            }
        }
        $this->setDataSource($new_source);
    }

    public function getDefault($row,$cs,$txt){
        foreach ($row as $key => $value) {
            $txt=str_replace('$'.$key, $value, $txt);
        } 
        return $txt;
    }

	public function setColumn($name = '', $title = '', $type = '', $default = '', $param = '', $class = '',$format=''){
	 	$column = [
            'name'    => $name,
            'title'   => $title,
            'type'    => $type,
            'default' => $default,
            'param'   => $param,
            'class'   => $class,
            'format'   => $format,
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

    public function addColumnButton($type = '',$title='',$href='',$class='',$icon='', $vals = ''){
        switch ($type) {
            case 'edit':
                //$key=url($this->controller.'/detail');
                $key=strtolower($this->controller.'_'.$this->action.'_edit');
                $result = [
                    'title' => '修改',
                    'icon'  => 'fa fa-pencil',
                    'class' => 'btn btn-xs btn-default ajax-get confirm',
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
        $result['vals']=$vals;
        $this->setDataList('columnbtn',$result);
        return $this;
    }
}