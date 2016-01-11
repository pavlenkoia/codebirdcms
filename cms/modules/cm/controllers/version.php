<?php
/**
 *
 */
class CmController_Version Extends Controller_Base
{

    public function index()
    {
        $this->setContent(Config::__("cm")->version);
    }
}

?>
