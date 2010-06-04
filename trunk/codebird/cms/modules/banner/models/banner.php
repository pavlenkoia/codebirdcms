<?php
/**
 *
 */

class BannerModel_Banner extends Model_Base
{
    public function getBanners()
    {
        return $this->getTable()->select("select * from banner order by name");
    }
}

?>
