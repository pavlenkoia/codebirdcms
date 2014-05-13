<?php
/**
 * 
 */

class GsearchModel_Gsearch extends Model_Base
{
    
    public function getResult($searchquery)
    {
        $site = Config::__('gsearch')->site;
        
        $site = $site ? $site : $_SERVER['HTTP_HOST'];
        
        $q = urlencode('site:'.$site.' '.$searchquery);
        
        $start = Utils::getGet('start') ? '&start='.Utils::getGet('start') : '';
        
        $url = 'http://ajax.googleapis.com/ajax/services/search/web?rsz=large&v=1.0&q='.$q.$start;

        try
        {
            $handle = fopen($url, 'rb');
        }
        catch (Exception $e)
        {
            return null;
        }
        
        $body = '';
        
        while (!feof($handle))
        {
            $body .= fread($handle, 8192);
        }
        
        fclose($handle);
        
        $data = json_decode($body);

        return $data;
    }

    public  function getApiUrl($searchquery)
    {
        $site = Config::__('gsearch')->site;

        $site = $site ? $site : $_SERVER['HTTP_HOST'];

        $q = urlencode('site:'.$site.' '.$searchquery);

        $start = Utils::getGet('start') ? '&start='.Utils::getGet('start') : '';

        $url = 'http://ajax.googleapis.com/ajax/services/search/web?rsz=large&v=1.0&q='.$q.$start;

        return $url.'&callback=?';
    }
}
?>
