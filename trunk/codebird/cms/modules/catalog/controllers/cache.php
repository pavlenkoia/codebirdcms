<?php
/*
 *
 */

class CatalogController_Cache extends Controller_Base
{

    public function index()
    {
    }

    public function show()
    {
        $mod = $this->args->mod;
        if(!$mod) return ;

        $cache_path = Registry::__instance()->mod_cache_path ? DS.Registry::__instance()->mod_cache_path : '';
        $cache = SITE_PATH.'files'.DS.'catalog'.DS.'modcache'.$cache_path.DS.$mod;

        $update_min = $this->args->update_min;
        if($update_min && is_file($cache))
        {
            $ft = filemtime($cache);

            if(time()-$ft > $update_min*60)
            {
                unlink($cache);
            }
        }

        if(is_file($cache))
        {
            $this->setContent(file_get_contents($cache));
        }
        else
        {
            $args = $this->args->toArray();
            $val = val($mod, $args);
            $this->setContent($val);
            
            $file = fopen ($cache,"w");
            fwrite ($file, $val);
            fclose ($file);
        }
    }

    public function clear()
    {
        $mod = $this->args->mod;

        $cache_path = Registry::__instance()->mod_cache_path ? DS.Registry::__instance()->mod_cache_path : '';

        if($mod)
        {
            $cache = SITE_PATH.'files'.DS.'catalog'.DS.'modcache'.$cache_path.DS.$mod;
            if(is_file($cache))
            {
                unlink($cache);
            }            
        }
        else
        {
            $cache = SITE_PATH.'files'.DS.'catalog'.DS.'modcache'.$cache_path;
            $files = scandir($cache);
            foreach($files as $file)
            {
                if (is_file($cache.DS.$file) && is_writable($cache.DS.$file) && $file != '.htaccess')
                {
                    unlink($cache.DS.$file);
                }

            }
        }
    }

}

?>