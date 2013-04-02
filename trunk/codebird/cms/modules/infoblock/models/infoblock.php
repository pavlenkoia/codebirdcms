<?php
/**
 *
 */

class InfoblockModel_Infoblock extends Model_Base
{
    public function getInfoblocks()
    {
        return $this->getTable()->select("select * from infoblock order by name");
    }
}

?>
