<?php
/**
 *
 */
class CatalogModel_Action extends Model_Base
{
    private $section_table_name  = 'catalog_section';

    private $section_table;

    public function setSectionTableName($section_table_name = null)
    {
        $this->section_table_name = $section_table_name ? $section_table_name :  'catalog_section';
        $this->section_table = null;
    }

    public function getSectionTable()
    {
        if(!$this->section_table)
        {
            $this->section_table = new Table($this->section_table_name);
        }
        return $this->section_table;
    }

    public function getSection($name)
    {
        $section = $this->getSectionTable()->getEntityAlias($name);

        if(!$section)
        {
            $section = $this->getSectionTable()->getEntity($name);
        }

        return  $section;
    }
}

?>
