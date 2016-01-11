<?php
/**
 *
 */

class SearchConfig extends Config_Base
{
    /**
     * Имя сайта для поиска
     * @var string
     */

    public $site = '';

    public $results_count = 10;

    public $template = "search.tpl.php";
}

?>
