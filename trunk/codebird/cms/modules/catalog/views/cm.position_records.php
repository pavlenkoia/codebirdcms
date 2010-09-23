<?php
    $res = array();
    $res_rows = array();
    $res_row = array();
    $fields = $table_meta['fields'];
    foreach($rows as $row)
    {
        $res_row['id'] = $row['id'];
        foreach($fields as $field)
        {
            if($field['type'] == 'image')
            {
                if($row[$field['field']] != null)
                {
//                    $res_row[$field['field']] = '<img src="/'.$row[$field['field']].'/cm-'.$row['id'].'.jpg?sid='.rand(0, 1000000).'"/>';
                    $res_row[$field['field']] = '<img src="'.get_cache_pic($row[$field['field']],75,75).'"/>';
                }
                else
                {
                    $res_row[$field['field']] = '';
                }
                continue;
            }
            elseif($field['type'] == 'date')
            {
               $res_row[$field['field']] = $row[$field['field']] ? date("d.m.Y",$row[$field['field']]) : null;

               continue;
            }
            elseif ($field['type'] == 'check')
            {
                if($row[$field['field']] == 1)
                {
                    $res_row[$field['field']] = 'да';
                }
                else
                {
                    $res_row[$field['field']] = '';
                }
                continue;
            }
            $res_row[$field['field']] = $row[$field['field']];
            if(isset($field['display']))
            {
                $res_row[$field['display']] = $row[$field['display']];
            }
        }
        $res_rows[] = $res_row;
    }
    $res['success'] = true;
    $res['results'] = $count;
    $res['rows'] = $res_rows;
    echo json_encode($res);
?>
