<?php
/**
 *
 */

 class ManualController_Cm Extends Controller_Base
{

    public function index()
    {
    }

    public function content()
    {
        $template = $this->createTemplate();

        $template->source = $this->config->source;

        $template->render();
    }

    public function tree()
    {
        $template = $this->createTemplate();

        $template->source = $this->config->source;

        $template->inner = $this->args->inner;

        $template->render();
    }
}

?>
