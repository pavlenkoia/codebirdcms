<?php
/*
 * Контроллер show баннера
 */

class BannerController_Show extends Controller_Base
{

    private function show($alias)
    {
        $cache_path = Registry::__instance()->banner_cache_path ? DS.Registry::__instance()->banner_cache_path : '';
        $cache = SITE_PATH.'files'.DS.'banner'.$cache_path.DS.$alias;
        if(is_file($cache))
        {
            $this->setContent(file_get_contents($cache));
        }
        else
        {
            $table = new Table('banner');
            
            $banner = $table->getEntityAlias($alias);
            
            if($banner)
            {
                $this->setContent($banner->html);

                $file = fopen ($cache,"w");
                fwrite ($file, $banner->html);
                fclose ($file);
            }
        }
    }

    public function __call($name, $args)
    {
       $this->show($name);
    }

    public function index()
    {

    }


}

?>
