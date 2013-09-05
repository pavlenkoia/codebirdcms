<?php
/**
 * Класс событий
 */
class Event
{
    private static $events = array();

    public static function AddHandler($event_id, $callback)
    {
        self::$events[$event_id][] = $callback;
    }

    public  static function Execute($event_id, $params)
    {
        if(!self::$events[$event_id]) return;

        foreach(self::$events[$event_id] as $handler)
        {
            if(is_array($handler))
            {
                $class = $handler[0];
                $fun = $handler[1];

                $event = new $class;

                $event->$fun($event_id, $params);
            }
            else
            {
                $handler($event_id, $params);
            }
        }
    }

    public static function HasHandlers($event_id)
    {
        if(self::$events[$event_id])
        {
            return true;
        }

        return false;
    }
}