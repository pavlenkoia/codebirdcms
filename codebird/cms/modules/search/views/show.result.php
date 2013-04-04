<?php
//echo '<pre>';print_r($searchresult);echo '</pre>';

if(!$results || !$resultCount)
{
    echo '<div class="searchresultcount">По вашему запросу ничего не найдено.</div>';
    if($searchresult->did_you_mean)
    {
        echo '<div class="did_you_mean">Возможно вы имели в виду <a href="'.$alias.'.html?q='.urlencode($searchresult->did_you_mean).'">'.$searchresult->did_you_mean.'</a></div>';
    }
    return;
}

echo '<div class="searchresultcount">Всего найдено: '.$resultCount.'</div>';

$formattedresults = '';
foreach($results as $result)
{
    $formattedresults .= '
            <div class="searchresult">
            <h3>'.$result->num.'. <a href="' . $result->url . '">' . $result->title . '</a></h3>
            <p class="resultdesc">' . $result->fulltxt . '</p>
            </div>';
}

echo $formattedresults;

$cursor = '';

echo '<div class="searchpager">';

if(count($pages) > 1)
{
    foreach($pages as $page)
    {
        if($page == $currentPageIndex)
        {
            $cursor .= ' <span class="current">'.$page.'</span>';
        }
        else
        {
            $cursor .= ' <span><a href="'.$alias.'.html?start='.$page.'&q='.urlencode($searchquery).'">'.$page.'</a></span>';
        }

    }
}

echo $cursor;

echo '</div>';

?>
