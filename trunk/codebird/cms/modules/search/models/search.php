<?php
/**
 *
 */

class SearchModel_Search extends Model_Base
{
    public function getResult($searchquery)
    {
        $site = Config::__('search')->site;

        $site = $site ? $site : $_SERVER['HTTP_HOST'];

        $results_count = Config::__('search')->results_count;

        $q = urlencode($searchquery);

        $start = intval(Utils::getGet('start')) ? '&start='.Utils::getGet('start') : '';

        $url = 'http://'.$site.'/cms/modules/search/html/sphider-utf/search_result.php?query='.$q.$start.'&search=1&type=or&results='.$results_count.'&domain='.urlencode('http://'.$site);

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
}

?>
