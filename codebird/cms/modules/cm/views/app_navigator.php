[
<?php
$modules = val("modmanager.cm.modules");
$sep = false;
$top = true;
foreach($modules as $name=>$module)
{
    if(isset($module['access']))
    {
        $access = explode(',',$module['access']);
        $res_access = false;
        foreach($access as $role)
        {
            if( val('security.inrole.'.$role))
            {
                $res_access = true;
                break;
            }
        }
        if(!$res_access) continue;
    }

    if($sep)
    {
        echo ',';
    }
    else
    {
        $sep = true;
    }    

    if(isset($module['module']))
    {
        $module_name = $module['module'];
    }
    else
    {
        $module_name = $name;
    }
    $param = isset($module['param']) ? '&param='.$module['param'] : '';

    mod($module_name.'.cm.navigator','module='.$module_name.'&title='.$module['title'].'&top='.$top.$param);
    $top = false;
}
?>
]
