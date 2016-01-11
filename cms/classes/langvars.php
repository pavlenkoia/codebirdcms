<?

  class Langvars {


   /**********************************
     * Метод замены месяцев кириллицей
     *********************************/
    public static function replaceMonth( $month ) {
  
      $new_month = '';
  
      switch ( $month ) {
  
        case '01' : $new_month = 'Января';
                    break;
  
        case '02' : $new_month = 'Февраля';
                    break;
  
        case '03' : $new_month = 'Марта';
                    break;
  
        case '04' : $new_month = 'Апреля';
                    break;
  
        case '05' : $new_month = 'Мая';
                    break;
  
        case '06' : $new_month = 'Июня';
                    break;
      
        case '07' : $new_month = 'Июля';
                    break;
  
        case '08' : $new_month = 'Августа';
                    break;
  
        case '09' : $new_month = 'Сентября';
                    break;
  
        case '10' : $new_month = 'Октября';
                    break;
  
        case '11' : $new_month = 'Ноября';
                    break;
  
        case '12' : $new_month = 'Декабря';
                    break;
  
        default : $new_month = 'Января';
  
      }
  
      return $new_month;
    
    }

      public static function getMonth($datestamp)
      {
        return self::replaceMonth(date('m',$datestamp));
      }


      /**
       * Вычисляем разницу между двумя timestamps
       * Параметры:
       *   string $date_start начальная дата в формате unix timestamp
       *   string $date_finish конечая дата в формате unix timestamp
       * Возвращает
       *   массив в следующем формате: 0 => секунды, 1 =>	минуты, 2 => часы, 3 => дни, 4 => месяцы, 5 => годы
       * @author zerkms (Ivan Kurnosov)
       */

      public static function calcPeriod($date_start, $date_finish) {
          $st = explode('-', date('d-m-Y-H-i-s', $date_start));
          $fin = explode('-', date('d-m-Y-H-i-s', $date_finish));

          if (($seconds = $fin[5] - $st[5]) < 0) {
              $fin[4]--;
              $seconds += 60;
          }

          if (($minutes = $fin[4] - $st[4]) < 0) {
              $fin[3]--;
              $minutes += 60;
          }

          if (($hours = $fin[3] - $st[3]) < 0) {
              $fin[0]--;
              $hours += 24;
          }

          if (($days = $fin[0] - $st[0]) < 0) {
              $fin[1]--;
              $days = date('t', mktime(1, 0, 0, $st[1], $st[0], $st[2])) - $st[0] + $fin[0];
          }

          if (($months = $fin[1] - $st[1]) < 0) {
              $fin[2]--;
              $months += 12;
          }

          $years = $fin[2] - $st[2];

          return array($seconds, $minutes, $hours, $days, $months, $years);
      }
  
  }