<?php
if (!function_exists('array_orderby')) {
    function array_orderby()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }
}
if (!function_exists('getTree')) {
    function getTree($data, $pid = 0)
    {
        $result = [];
        $i = 0;
        foreach ($data as $k => $v) {
            if ($v['pid'] == $pid) {
                $result[$i]['id'] = $v['id'];
                $result[$i]['name'] = $v['title'];
                $result[$i]['file'] = $v['file'];
                $result[$i]['sort'] = $v['sort'];
                $result[$i]['child'] = getTree($data, $v['id']);
                $i++;
            }
        }
        $result = array_orderby($result, 'sort', SORT_ASC);
        return $result;
    }
}

function sortReset($summary, $sorts, $data, $level=0){
    $result = [];
    if(0 === $level && in_array(0, $sorts)){
        foreach ($summary as $k=>$v){
            if(isset($v['id'])){
                $result[] = ['sort'=>$k, 'id'=>$v['id']];
            }
        }
    }
    $diffs = $sorts;
    if($key = array_search(0, $diffs)){
        unset($diffs[$key]);
    }
    if(count($diffs) > 0){
        foreach ($summary as $k=>$v){
            if(isset($v['id'])){
                if(in_array($v['id'], $sorts) && $v['child']){
                    foreach ($v['child'] as $key=>$val){
                        if(isset($val['id'])){
                            $result[] = ['sort'=>$key, 'id'=>$val['id']];
                        }
                    }
                }
                if($v['child']){
                    $level++;
                    sortReset($v['child'], $sorts, $data, $level);
                }
            }
        }
    }
    return $result;
}

function catalogTree($doc, $array, $data, $files, $titles, $pid=0){
    $addId = null;
    foreach ($array as $v){
        if(isset($v['id']) && array_key_exists($v['id'], $files)){
            $addId = $v['id'];
            if($v['name'] != $titles[$v['id']] || $v['file'] != $files[$v['id']]){
                $res = \app\doc\model\DocCatalog::update(['title'=>$v['name'], 'file'=>$v['file'], 'sort'=>$v['sort']], ['id'=>$v['id']]);
            }
        }else if(!isset($v['id'])){
            $res = \app\doc\model\DocCatalog::create(['did'=>$doc, 'pid'=>$pid, 'title'=>$v['name'], 'file'=>$v['file'], 'sort'=>$v['sort']]);
            $addId = $res->id;
            $res = \app\doc\model\DocDetail::create(['did'=>$doc, 'cid'=>$res->id]);
        }
        if($v['child']){
            catalogTree($doc, $v['child'], $data, $files, $titles, $addId);
        }
    }
}
