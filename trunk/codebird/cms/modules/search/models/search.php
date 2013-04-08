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

        $url = 'http://'.$site.'/cms/modules/search/html/sphider-utf/search_result.php?query='.$q.$start.'&search=1&type=or&results='.$results_count.'&domain='.$site;

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

    public function httpIndex($domain, $reindex = true)
    {
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/cms/modules/search/html/sphider-utf/admin/spider.php';

        $params = array(
            'soption' => 'full',
            'reindex' => $reindex ? 1 : 0,
            'url' => 'http://'.$domain.'/',
            'from_search' => 1
        );
        $params = http_build_query($params);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,  $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $content = curl_exec( $ch );
        curl_close( $ch );

        return $content;
    }

    public function getSites()
    {
        $table = new Table('sphider_sites','site_id');

        $rows = $table->select('select a.site_id, a.url, a.indexdate, b.site_id as pending from sphider_sites a left outer join sphider_pending b on a.site_id=b.site_id');

        return $rows;
    }

    public function getSite($id)
    {
        $table = new Table('sphider_sites','site_id');

        $rows = $table->select('select a.site_id as id, a.url, a.indexdate, b.site_id as pending from sphider_sites a left outer join sphider_pending b on a.site_id=b.site_id where a.site_id=:id',array('id'=>$id));

        if(count($rows) > 0)
        {
            return $rows[0];
        }
        else
        {
            return null;
        }
    }
}

?>
