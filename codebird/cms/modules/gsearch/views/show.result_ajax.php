<?if($ajax_result){?>

<?if(!$searchresult){?>
    <div class="searchresultcount">По вашему запросу ничего не найдено.</div>
<?return;}?>

<div class="searchresultcount">Всего найдено: <?=$resultCount?></div>

<?foreach($results as $result){?>
        <div class="searchresult">
            <h3><a href="<?=$result['unescapedUrl']?>"><?=$result['titleNoFormatting']?></a></h3>
            <p class="resultdesc"><?=$result['content']?></p>
        </div>
<?}?>

<div class="searchpager">

<?foreach($pages as $page){?>
    <?if($page->label == $currentPageIndex+1){?>
        <span class="current">'.$page->label.'</span>
    <?}else{?>
        <span><a href="<?=$page_alias.'.html?start='.$page['start'].'&q='.urlencode($searchquery)?>"><?=$page['label']?></a></span>
    <?}?>
<?}?>

</div>

<?//echo '<pre>';print_r($searchresult);echo '</pre>';?>

<?}else{?>
<div id="result_ajax"></div>
<script>
    $(function(){
        var apiURL = '<?=$apiUrl?>';
        $.getJSON(apiURL,{},function(r){
            //console.log(r);
            $.ajax({
                url: '/ajax/gsearch.show.result_ajax',
                type: 'POST',
                data: {
                    results: r,
                    page_alias: '<?=Utils::GetVar('alias')?>',
                    q: '<?=htmlspecialchars($searchquery)?>'
                },
                success: function(data){
                    console.log(data);
                    $('#result_ajax').html(data);
                }
            });
        });

    });
</script>
<?}?>