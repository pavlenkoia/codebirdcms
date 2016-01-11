<?php
/**
 * Класс установщик модулей
 */

class Installer
{
    private $installCode = 0;

    private $installMessage = "";

    public function getInstallCode()
    {
        return $this->installCode;
    }

    public function getInstallMessage()
    {
        return $this->installMessage;
    }

    /**
     * Инсталяция модуля
     * @param Install_Base $install экземпляр класса инсталятора модуля
     */
    public function install($install)
    {
        $this->installCode = 0;
        $this->installMessage = "";

        try
        {
            $errorInfo = $this->execSql($install->sql);
            if($errorInfo != "")
            {
                throw new Exception($errorInfo);
            }

            $errorInfo = $install->exec_sql();
            if($errorInfo != "")
            {
                throw new Exception($errorInfo);
            }

            foreach($install->dirs as $dir)
            {
                $path = SITE_PATH.DS.'files'.DS.$dir;
                if(!file_exists($path))
                {
                    mkdir(SITE_PATH.DS.'files'.DS.$dir, 0777, true);
                }
            }

            if($install->config != "")
            {
                $file = SITE_PATH.DS.'config'.DS.$install->getModule().'.config.xml';
                if(!file_exists($file))
                {
                    $xml = simplexml_load_string($install->config);
                    $xml->asXML($file);
                    chmod($file, 0777);
                }
            }
        }
        catch(Exception $e)
        {
            $this->installCode = 1;
            $this->installMessage = $e->getMessage();
        }
    }

    private function execSql($sql)
    {
        $errorInfo = "";

        $sqls = explode(";", $sql);

        $db = Registry::__instance()->db;

        foreach($sqls as $sql)
        {
            if(trim($sql) == "") continue;

            $psql = $db->prepare($sql);

            if(!$psql->execute())
            {
                $errorInfos = $psql->errorInfo();
                $errorInfo .= $errorInfos[2]."\n";
            }
        }

        return $errorInfo;
    }
}
?>
