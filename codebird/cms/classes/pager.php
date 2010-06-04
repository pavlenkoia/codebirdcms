<?php

class Pager
{

    public $itemsCount = 0,
    $limit,
    $pageCount,
    $currentPage,
    $currentPageNumber,
    $currentPageName,
    $offset,
    $direction,
    $firstPage,
    $lastPage,
    $formName,
    $page;

    function Pager($itemsCount, $itemsPerPage, $formName, $page = 0, $direction = 1)
    {
        $this->formName = empty($formName) ? "page" : $formName;
        $this->page = empty($page) ? $_REQUEST[$this->formName] : $page;
        $this->itemsCount = (int)$itemsCount;
        $this->limit = (int)$itemsPerPage;
        $this->pageCount = ceil($this->itemsCount / $this->limit);
        if((int)$direction < 0)
        {
            $this->currentPage = !(int)$this->page && $this->page != "0" ? $this->pageCount : $this->page;
            if($this->currentPage > $this->pageCount)
            {
                $this->currentPage = $this->pageCount;
            } else
            {
                if($this->currentPage < 1) $this->currentPage = 1;
            }
            $this->currentPageNumber = $this->pageCount - $this->currentPage + 1;
            $this->currentPageName = $this->currentPageNumber;
            if($this->pageCount && $this->currentPage < $this->pageCount)
            {
                $this->isFullPage = $this->itemsCount % $this->limit ? 1 : 0;
                $this->offset = $this->itemsCount % $this->limit + ($this->pageCount - $this->currentPage - $this->isFullPage) * $this->limit;
            } else
            {
                $this->offset = 0;
            }
            $this->direction = "-1";
            $this->firstPage = $this->pageCount;
            $this->lastPage = 1;
        } else
        {
            $this->currentPage = !(int)$this->page && $this->page != "0" ? 1 : $this->page;
            if($this->currentPage > $this->pageCount)
            {
                $this->currentPage = $this->pageCount;
            } else
            {
                if($this->currentPage < 1) $this->currentPage = 1;
            }
            $this->currentPageNumber = $this->currentPage;
            $this->currentPageName = $this->currentPage;
            $this->direction = "+1";
            $this->firstPage = 1;
            $this->lastPage = $this->pageCount;
            $this->offset = $this->pageCount ? ($this->currentPage - 1) * $this->limit : 0;
        }
    }

    function show($params = 0)
    {
        $sReturn = "";
        if($this->pageCount >1)
        {
            if(isset($params["mode"])) $this->mode = $params["mode"];
            $this->navCount = (int)$params["navCount"] ? $params["navCount"] : 5;
            $this->firstNav = $this->currentPageNumber - floor($this->navCount / 2);
            if($this->firstNav < 1) $this->firstNav = 1;
            $this->lastNav = $this->firstNav + $this->navCount - 1;
            if($this->lastNav > $this->pageCount)
            {
                $this->lastNav = $this->pageCount;
                $this->firstNav = $this->lastNav - $this->navCount;
                if($this->firstNav < 1) $this->firstNav = 1;
            }
            $this->separator = isset($params["separator"]) ? $params["separator"] : "&hellip;";
            $qupos = strpos($params["targetUrl"], "?");
            $this->urlSeparator = $qupos !== false ? "&" : "?";
            if(isset($params["tagName"])) $sReturn .= "<" . $params["tagName"] . " " . $params["tagAttr"] . " >";
            $this->title = isset($params["title"]) ? $params["title"] : "Страницы: ";
            if($this->mode == "html")
            {
                $sReturn .= $this->title;
            } else
            {
                $sReturn = "<title>" . $this->title . "</title>";
                if(isset($params["leftDivider"])) $sReturn .= "<left-divider>" . $params["leftDivider"] . "</left-divider>";
                if(isset($params["rightDivider"])) $sReturn .= "<right-divider>" . $params["rightDivider"] . "</right-divider>";
            }
            if($this->currentPage != $this->firstPage)
            {
                $backName = isset($params["backName"]) ? $params["backName"] : "&larr; Назад";
                $sReturn .= $this->_printNavItem("back", $backName, $params["targetUrl"], $this->urlSeparator, $this->currentPage - $this->direction);
                if($this->mode == "html" && isset($params["leftDivider"])) $sReturn .= $params["leftDivider"];
            }
            if($this->firstNav > 1)
            {
                $sReturn .= $this->_printNavItem("first", 1, $params["targetUrl"], $this->urlSeparator, $this->firstPage);
                if($this->firstNav > 2)    $sReturn .= $this->_printNavItem("separator", $this->separator);
            }
            for ($i = $this->firstNav; $i <= $this->lastNav; $i++)
            {
                $current = "";
                $ipage = $this->direction < 0 ? $this->pageCount - $i + 1 : $i;
                if($ipage == $this->currentPage) $current = "current";
                $sReturn .= $this->_printNavItem($current, $i, $params["targetUrl"], $this->urlSeparator, $ipage);
            }
            if($this->lastNav < $this->pageCount)
            {
                if($this->lastNav < $this->pageCount - 1) $sReturn .= $this->_printNavItem("separator", $this->separator);
                $sReturn .= $this->_printNavItem("last", $this->pageCount, $params["targetUrl"], $this->urlSeparator, $this->lastPage);
            }
            if($this->currentPage != $this->lastPage)
            {
                if($this->mode == "html")
                {
                    $sReturn .= isset($params["rightDivider"]) ? $params["rightDivider"] : "|";
                }
                $forwardName = isset($params["forwardName"]) ? $params["forwardName"] : "Дальше &rarr;";
                $sReturn .= $this->_printNavItem("forward", $forwardName, $params["targetUrl"], $this->urlSeparator, $this->currentPage + $this->direction);
            }
            if(isset($params["tagName"])) $sReturn .= "</" . $params["tagName"] . " >";
        }
        return $sReturn;
    }

    function _printNavItem($type, $name, $url = "", $urlSeparator = "", $pageNum = "")
    {
        $sReturn = "";
        if($this->mode == "html")
        {
            if($type == "separator")
            {
                $sReturn = "\n<span class=\"separator\">".$name."</span>\n";
            } elseif($type != "current" && (int)$pageNum)
            {
                $sReturn .= "\n<a href=\"";
                $sReturn .= !empty($url) ? $url : "./";
                if($pageNum != $this->firstPage) $sReturn .= $urlSeparator.$this->formName."=".$pageNum;
                $sReturn .= "\"";
                if(empty($type) || $type == "first" || $type == "last") $sReturn .= " class=\"scrollerPage\"";
                $sReturn .= ">".$name."</a>\n";
            } else
            {
                $sReturn = "\n<span class=\"scrollerCurrentPage\">".$name."</span>\n";
            }
        } else
        {
            $sReturn = "<page ";
            if(isset($type)) $sReturn .= 'type="'.$type.'" ';
            if($type != "current" && !empty($pageNum))
            {
                $sReturn .= 'href="';
                !empty($url) ? $sReturn .= $url : $sReturn .= "./";
                if($pageNum != $this->firstPage) $sReturn .= $urlSeparator.$this->formName.'='.$pageNum;
                $sReturn .= '" num="'.$name.'" ';
            }
            $sReturn .= "/>";
        }
        return $sReturn;
    }

    public static function html($count, $page_size)
    {
        $page = Utils::getGET("page") ? Utils::getGET("page") : 1;
        $page = $page < 1 ? 1 : $page;

        $pager = new Pager($count, $page_size, 'page', $page);
        $uri = $_SERVER['REQUEST_URI'];
        $uri = preg_replace("/[&?]*page=\d*/","",$uri);
        $params = array(
            "targetUrl" => $uri,
            "navCount" => 5,
            "mode" => "html",
            "title" => "Страницы: ",
            "backName" => "Пред",
            "forwardName" => "След"
        );
        echo $pager->show($params);
    }
}
?>