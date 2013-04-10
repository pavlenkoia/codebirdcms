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

        $url = 'http://'.$site.'/cms/modules/search/html/sphider-utf/search_result.php?query='.$q.$start.'&search=1&type=and&results='.$results_count.'&domain='.$site;

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
        $site = Config::__('search')->site;
        $site = $site ? $site : $_SERVER['HTTP_HOST'];

        $url = 'http://'.$site.'/cms/modules/search/html/sphider-utf/admin/spider.php';

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

    public function Index($domain, $reindex = true)
    {
        $url = 'http://'.$domain.'/';
        $soption = 'full';
        $reindex = $reindex ? 1 : 0;

        ob_start();

        include(ROOT.'/cms/modules/search/html/sphider-utf/admin/spider.php');

        return ob_get_clean();
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

    public function deleteSite($id)
    {
        $table = new Table('sphider_sites','site_id');

        $oSite = $table->getEntity($id);

        if($oSite)
        {
            $site_id = $oSite->site_id;

            $table->execute("delete from sphider_site_category where site_id=:site_id",array('site_id'=>$site_id));

            $rows = $table->select("select link_id from sphider_links where site_id=:site_id",array('site_id'=>$site_id));

            $todelete = array();

            foreach($rows as $row)
            {
                $todelete[]=$row['link_id'];
            }

            if (count($todelete)>0) {
                $todelete = implode(",", $todelete);
                for ($i=0;$i<=15; $i++)
                {
                    $char = dechex($i);
                    $query = "delete from sphider_link_keyword$char where link_id in($todelete)";
                    $table->execute($query);
                }
            }

            $table->execute("delete from sphider_links where site_id=:site_id",array('site_id'=>$site_id));

            $table->execute("delete from sphider_pending where site_id=:site_id",array('site_id'=>$site_id));

            $table->delete($oSite);
        }
    }

    public  function addSite($url)
    {
        $table = new Table('sphider_sites','site_id');

        $site_id = null;

        $compurl = parse_url("".$url);

        if ($compurl['path']=='')
        {
            $url = $url."/";
        }

        $rows = $table->select("select site_ID from sphider_sites where url=:url",array('url'=>$url));

        if(!count($rows))
        {
            $object = $table->getEntity();
            $object->url = $url;

            $site_id = $table->save($object);

            $domain = str_replace("http:",'',$url);
            $domain = str_replace("/",'',$domain);

            $table2 = new Table('sphider_domains','domain_id');
            $rows = $table2->select("select site_ID from sphider_domains where domain=:domain",array('domain'=>$domain));
            if(!count($rows))
            {
                $object2 = $table2->getEntity();
                $object2->domain = $domain;
                $table2->save($object2);
            }
        }

        return $site_id;
    }
}

?>
