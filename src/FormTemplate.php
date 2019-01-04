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


class FormTemplate  extends Template{

    /**
     * 默认路径
     * @access public
     * @return string
     */
	public function getTemplateLayout(){
        if(isset($this->view->formlayout)){
            return $this->view->formlayout;
        } 
	}

    public function render(){ 
        //合并数据源
        $source=$this->datasource;
        if(!is_null($source)){
            if($source instanceof \com\membership\core\IUser){
                $source=ManagerService::getuser($source->userid);
            }else if(!is_array($source)){
                $source=((array)$source);//转为数据
            }
        } 
        $ts=$this->getData('items'); 
        if(!is_null($ts)){
            foreach ($ts as $key => &$item) {
                if(isset($source[$item['name']])){ 
                    $item['value']=$source[$item['name']];
                } 
                $ts[$key]=$item;
            } 
        }
        
        $this->setData('forms',$ts);
    }

    public function getItem($name){
        return $this->datasource[$name];
    }

     public function addHidden($name = '', $default = '',$script='')
    {
        $type = 'hidden';
        $item = [
            'type'        => $type,
            'name'        => $name, 
            'value'       => $default,
            'script'      => $script,
        ]; 

        $this->setDataList('items',$item);
        return $this;
    }

    public function addHr($name = '', $default = '',$script='')
    {
        $type = 'hr';
        $item = [
            'type'        => $type,
            'name'        => $name, 
            'value'       => $default,
            'script'      => $script,
        ]; 

        $this->setDataList('items',$item);
        return $this;
    }

    public function addControl($name = '', $content='')
    {
        $type = 'control';
        $item = [
            'type'        => $type,
            'content'     => $content, 
        ]; 

        $this->setDataList('items',$item);
        return $this;
    }

    public function addLabel($name = '', $title = '', $tips = '', $default = '', $extra_class = '',$script='')
    {
        $item = [
            'type'        => 'label',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_class' => $extra_class,
            'script'      => $script,
        ]; 
        $this->setDataList('items',$item);
        return $this;
    }

    public function addDate($name = '', $title = '', $tips = '', $default = '', $attr = [], $extra_attr = '', $extra_class = '',$script='')
    {
        $item = [
            'type'        => 'date',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'attr'        => $attr,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'extra_label_class' => $extra_attr == 'disabled' ? 'css-input-disabled' : '',
            'script'      => $script,
        ]; 

        $this->setDataList('items',$item);
        return $this;
    }

    public function addDateRange($name = '', $title = '', $tips = '', $default = '', $attr = [], $extra_attr = '', $extra_class = '',$script='')
    {
        $item = [
            'type'        => 'daterange',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'attr'        => $attr,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'extra_label_class' => $extra_attr == 'disabled' ? 'css-input-disabled' : '',
            'script'      => $script,
        ]; 

        $this->setDataList('items',$item);
        return $this;
    }

    public function addSwitch($name = '', $title = '', $tips = '', $default = '', $attr = [], $extra_attr = '', $extra_class = '',$script='')
    {
        $item = [
            'type'        => 'switch',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'attr'        => $attr,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'extra_label_class' => $extra_attr == 'disabled' ? 'css-input-disabled' : '',
            'script'      => $script,
        ]; 

        $this->setDataList('items',$item);
        return $this;
    }

    public function addSelect($name = '', $title = '', $tips = '', $options = [], $default = '', $extra_attr = '', $extra_class = '',$script='',$default_option='')
    {
        $type = 'select';

        $item = [
            'type'        => $type,
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'options'     => $options,
            'value'       => $default,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'script'      => $script,
            'default_option'=>$default_option
        ]; 

        $this->setDataList('items',$item);
        return $this;
    }

    public function addRadio($name = '', $title = '', $tips = '', $options = [], $default = '', $attr = [], $extra_attr = '', $extra_class = '',$script='')
    {
        $item = [
            'type'        => 'radio',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'options'     => $options == '' ? [] : $options,
            'value'       => $default,
            'attr'        => $attr,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'extra_label_class' => $extra_attr == 'disabled' ? 'css-input-disabled' : '',
            'script'      => $script,
        ];

        $this->setDataList('items',$item);
        return $this;
    }

    public function addPhone($name = '', $title = '', $tips = '', $default = '', $group = [], $extra_attr = '', $extra_class = '',$script='')
    {
        $item = [
            'type'        => 'phone',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'group'       => $group,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'script'      => $script,
        ]; 

        $this->setDataList('items',$item);
        return $this;
    }

    public function addColor($name = '', $title = '', $tips = '', $default = '', $group = [], $extra_attr = '', $extra_class = '',$script='')
    {
        $item = [
            'type'        => 'color',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'group'       => $group,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'script'      => $script,
        ]; 

        $this->setDataList('items',$item);
        return $this;
    }

    public function addText($name = '', $title = '', $tips = '', $default = '', $group = [], $extra_attr = '', $extra_class = '',$script='')
    {
        $item = [
            'type'        => 'text',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'group'       => $group,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'script'      => $script,
        ]; 

        $this->setDataList('items',$item);
        return $this;
    }

    public function addEmail($name = '', $title = '', $tips = '', $default = '', $group = [], $extra_attr = '', $extra_class = '',$script='')
    {
        $item = [
            'type'        => 'email',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'group'       => $group,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'script'      => $script,
        ]; 

        $this->setDataList('items',$item);
        return $this;
    }

    public function addTags($name = '', $title = '', $tips = '', $default = '', $group = [], $extra_attr = '', $extra_class = '',$script='')
    {
        $item = [
            'type'        => 'tags',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'group'       => $group,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'script'      => $script,
        ]; 

        $this->setDataList('items',$item);
        return $this;
    }

    public function addCheckbox($name = '', $title = '', $tips = '', $options = [], $default = '', $attr = [], $extra_attr = '', $extra_class = '',$script='')
    {
        $item = [
            'type'        => 'checkbox',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'options'     => $options == '' ? [] : $options,
            'value'       => $default,
            'attr'        => $attr,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'extra_label_class' => $extra_attr == 'disabled' ? 'css-input-disabled' : '',
            'script'      => $script,
        ]; 

        $this->setDataList('items',$item);
        return $this;
    }

    public function addUeditor($name = '', $title = '', $tips = '', $default = '', $width = '0',$height='300',$script='')
    {
        $item = [
            'type'        => 'ueditor',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'width'       => $width,
            'height'      => $height,
            'script'      => $script,
        ];  
        $this->setDataList('items',$item);
        return $this;
    }

    public function addStatic($name = '', $title = '', $tips = '', $default = '', $hidden = '', $extra_class = '',$script='')
    {
        $item = [
            'type'        => 'static',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'hidden'      => $hidden === true ? ($default == '' ? true : $default) : $hidden,
            'extra_class' => $extra_class,
            'script'      => $script,
        ]; 

        $this->setDataList('items',$item);
        return $this;
    }

    public function addPassword($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '',$script='')
    {
        $item = [
            'type'        => 'password',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'script'      => $script,
        ]; 

        $this->setDataList('items',$item);
        return $this;
    }

    public function addImage($name = '', $title = '', $tips = '', $default = '', $size = '', $ext = '', $extra_class = '',$script='')
    {
        $size = 5* 1024;
        $ext  = $ext != '' ? $ext : '.gif,.png,.jpg';

        $item = [
            'type'        => 'image',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'size'        => $size,
            'ext'         => $ext,
            'extra_class' => $extra_class,
            'script'      => $script,
        ]; 

        $this->setDataList('items',$item);
        return $this;
    }

    public function addLine($name = '', $title = '', $tips = '', $default = '', $size = '', $ext = '', $extra_class = '',$script='')
    { 
        $item = [
            'type'        => 'line',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'size'        => $size,
            'ext'         => $ext,
            'extra_class' => $extra_class,
            'script'      => $script,
        ]; 

        $this->setDataList('items',$item);
        return $this;
    }

    public function addTextarea($name = '', $title = '', $tips = '', $default = '', $size = '', $ext = '', $extra_class = '',$script='')
    { 
        $item = [
            'type'        => 'textarea',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'size'        => $size,
            'ext'         => $ext,
            'extra_class' => $extra_class,
            'script'      => $script,
        ]; 

        $this->setDataList('items',$item);
        return $this;
    }

	public function addFormItem($type = '', $name = '')
    {
        if ($type != '') {
            // 获取所有参数值
            $args = func_get_args(); 
            array_shift($args);  
            $method = 'add'. ucfirst($type);
            call_user_func_array([$this, $method], $args);
        }
        return $this;
    }

	public function addFormItems($columns = [])
    {
        if (!empty($columns)) {
            foreach ($columns as $column) {
                call_user_func_array([$this, 'addFormItem'], $column);
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
                $key=$this->controller.'Edit';
                $result = [
                    'title' => '修改',
                    'icon'  => 'fa fa-pencil',
                    'class' => 'btn btn-xs btn-default ajax-get confirm',
                    //'href'  => "$key?id=#id#"
                    'href'  => "javascript:app.url(#id#,'$key')"
                ];
                break;
            case 'delete':
                $key=$this->controller.'Delete';
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

    public function submit($a='',$action='',$url='',$title='保存',$btntype='submit',$btnclass='btn btn-primary'){
        
        $v=empty($a)?$this->action:$a;
        $result = [
                    'title'     => $title,
                    'btntype'   => $btntype,
                    'btnclass'  => $btnclass,
                    'url'       => $url,
                    'action'    => $action, 
                    'onsubmit'  =>empty($onsubmit)?"return app.$btntype(this,'$v','$url');":$onsubmit,
                ];
        $this->setData('submit_data',$result);
        return $this;
    }
}