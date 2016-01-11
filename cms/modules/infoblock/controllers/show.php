<?php
/*
 * Контроллер show баннера
 */

class InfoblockController_Show extends Controller_Base
{

    private function show($alias)
    {
        $cache_path = Registry::__instance()->infoblock_cache_path ? DS.Registry::__instance()->infoblock_cache_path : '';
        $cache = SITE_PATH.'files'.DS.'infoblock'.$cache_path.DS.$alias;
        if(is_file($cache))
        {
            $this->setContent(file_get_contents($cache));
        }
        else
        {
            $table = new Table('infoblock');
            
            $infoblock = $table->getEntityAlias($alias);
            
            if($infoblock)
            {
                $this->setContent($infoblock->html);

                $file = fopen ($cache,"w");
                fwrite ($file, $infoblock->html);
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
