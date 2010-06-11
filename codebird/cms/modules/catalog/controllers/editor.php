<?php
/*
 * Контроллер editor каталога
 */
class CatalogController_Editor extends Controller_Base
{
    private function editor_access()
    {
        return $this->config->front_editor && $this->login();
    }

    public function index()
    {
    }

    public function header()
    {
        if(!$this->editor_access()) return;

        $template = $this->createTemplate();
        $template->render();
    }

    public function btn_position()
    {
        if(!$this->editor_access()) return;

        $template = $this->createTemplate();

        $template->section_id = $this->args->section_id;

        $template->position_id = $this->args->position_id;

        $section = $this->getData()->getSection($this->args->section_id);

        $template->table_id = $section ? $section->position_table : null;

        $template->render();
    }

    public function btn_position_add()
    {
        if(!$this->editor_access()) return;

        $template = $this->createTemplate();

        $template->table_id = $this->args->table_id;

        $template->section_id = $this->args->section_id;

        $template->position_id = $this->args->position_id;

        $template->render();
    }

    public function position_edit_form()
    {
        if(!$this->editor_access()) return;

        $position_id = Utils::getPost('position_id');

        $section_id = Utils::getPost('section_id');

        $data = $this->getData();

        $section = $data->getSection($section_id);

        if(!$section) return;

        $table_meta = $data->getTableMeta($section->position_table);
        
        if(!$table_meta) return;

        $table = new Table($table_meta['table']);

        $position = $position_id ? $table->getEntity($position_id) : $table->getEntity();

        $template = $this->createTemplate();

        $template->table_id = $section->position_table;
        
        $template->section_id = $section_id;

        $template->fields = $table_meta['fields'];

        $template->position_id = $position_id;

        $template->position = $position;

        $template->render();
    }

    public function btn_section()
    {
        if(!$this->editor_access()) return;

        $template = $this->createTemplate();

        $template->section_id = $this->args->section_id;

        $template->render();
    }

    public function section_edit_form()
    {
        if(!$this->editor_access()) return;

        $section_id = Utils::getPost('section_id');

        $data = $this->getData();

        $section = $data->getSection($section_id);

        if(!$section) return;

        $table_meta = $data->getTableMetaSection($section->section_table);

        if(!$table_meta) return;

        $table = new Table($table_meta['table']);

        $section_data = $table->getEntity($section_id);

        $template = $this->createTemplate();

        $template->table_id = $section->section_table;

        $template->section_id = $section_id;

        $template->fields = $table_meta['fields'];

        $template->section = $section;

        $template->section_data = $section_data;

        $template->render();
    }

    public function btn_section_add()
    {
        if(!$this->editor_access()) return;

        $template = $this->createTemplate();

        $template->section_id = $this->args->section_id;

        $template->render();
    }

    public function section_add_form()
    {
        if(!$this->editor_access()) return;

        $section_id = Utils::getPost('section_id');

        $data = $this->getData();

        $parent_section = $data->getSection($section_id);

        if(!$parent_section) return;

        $template = $this->createTemplate();

        $template->parent_section = $parent_section;

        $template->render();
    }

    public function btn_page()
    {
        if(!$this->editor_access()) return;

        $template = $this->createTemplate();

//        $template->page_id = $this->args->page_id;

        $template->render();
    }

    public function page_edit_form()
    {
        if(!$this->editor_access()) return;

        $page_id = Utils::getPost('page_id');

        $table = new Table('pages');

        $page = $table->getEntityAlias($page_id);

        if(!$page) return;

        $template = $this->createTemplate();

        $template->page = $page;

        $template->render();
    }

    public function save_page()
    {
        if(!$this->editor_access()) return;

        $res = array();

        try
        {
            $id = Utils::getVar('id');

            $title = Utils::getVar('title');

            $content = Utils::getVar('content');

            $table = new Table('pages');

            $page = $table->getEntity($id);

            if(!$page) throw new Exception('Объект уже удален');

            $page->title = $title;

            if($content !== null)
            {
                $page->content = $content;
            }

            $table->save($page);

            if($table->errorInfo)  throw new Exception($table->errorInfo);

            $res['success'] = true;
            $res['msg'] = 'Готово';
        }
        catch(Exception $e)
        {
            $res['success'] = false;
            $res['msg'] = $e->getMessage();
        }


        $this->setContent(json_encode($res));
    }
}
