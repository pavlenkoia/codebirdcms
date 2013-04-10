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
  
  }