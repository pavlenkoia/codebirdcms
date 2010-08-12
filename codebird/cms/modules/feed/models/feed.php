<?php
/**
 *
 */

class FeedModel_Feed extends Model_Base
{
    public function getFeeds()
    {
        return $this->getTable()->select("select * from feed order by name");
    }

    public function getFeed($alias)
    {
        return $this->getTable()->getEntityAlias($alias);
    }

    public function getFeed_items($feed)
    {
        return $this->getTable()->select("select * from feed_item where feed_id=:feed_id order by position", array("feed_id"=>$feed->id));
    }

    public function import($feed)
    {
        if($feed->datestamp_update != null && time() - $feed->datestamp_update < $feed->interval_update * 60 )
        {
            return;
        }

        $url = $feed->url;

        if($url == null) return;

        $rss = simplexml_load_file($url);

        if($rss===false) return;

        $table = new Table("feed_item");

        $table->execute("delete from feed_item where feed_id=:feed_id", array("feed_id"=>$feed->id));

        $position = 0;
        foreach ($rss->channel->item as $item)
        {
            $feed_item = $table->getEntity();
            $feed_item->feed_id = $feed->id;
            $feed_item->position = $position++;
            $feed_item->title = $item->title;
            $feed_item->description = $item->description;
            $feed_item->link = $item->link;
            $feed_item->pubdate = strtotime($item->pubDate);
            $table->save($feed_item);
        }

        $feed->datestamp_update = time();
        $this->getTable()->save($feed);
    }
}

?>

