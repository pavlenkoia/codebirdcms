<?php
/* 
 * 
 */

class Utils
{
/**
 * Возвращает значение параметра запроса из глобального массива $_REQUEST
 *
 * @param string $name имя параметра
 * @return string возвращает значение параметра запроса, если нет - null
 */

    public static function getVar($name)
    {
        if(isset($_REQUEST[$name]))
        {
            return $_REQUEST[$name];
        }
        return null;
    }

    /**
     * Возвращает значение параметра POST запроса из глобального массива $_POST
     *
     * @param string $name имя параметра
     * @return string возвращает значение параметра POST запроса, если нет - null
     */

    public static function getPost($name)
    {
        if(isset($_POST[$name]))
        {
            return $_POST[$name];
        }
        return null;
    }

    /**
     * Возвращает значение параметра GET запроса из глобального массива $_GET
     *
     * @param string $name имя параметра
     * @return string возвращает значение параметра GET запроса, если нет - null
     */

    public static function getGET($name)
    {
        if(isset($_GET[$name]))
        {
            return $_GET[$name];
        }
        return null;
    }

    /**
     * Перевод текста из кириллицы в транслит
     *
     * @param string $string входная строка
     * @return string транслитированная строка
     */

    public static function translit($string)
    {
        $table = array(
            'А' => 'A',
            'Б' => 'B',
            'В' => 'V',
            'Г' => 'G',
            'Д' => 'D',
            'Е' => 'E',
            'Ё' => 'YO',
            'Ж' => 'ZH',
            'З' => 'Z',
            'И' => 'I',
            'Й' => 'J',
            'К' => 'K',
            'Л' => 'L',
            'М' => 'M',
            'Н' => 'N',
            'О' => 'O',
            'П' => 'P',
            'Р' => 'R',
            'С' => 'S',
            'Т' => 'T',
            'У' => 'U',
            'Ф' => 'F',
            'Х' => 'H',
            'Ц' => 'C',
            'Ч' => 'CH',
            'Ш' => 'SH',
            'Щ' => 'CSH',
            'Ь' => '',
            'Ы' => 'Y',
            'Ъ' => '',
            'Э' => 'E',
            'Ю' => 'YU',
            'Я' => 'YA',

            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'yo',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'j',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'csh',
            'ь' => '',
            'ы' => 'y',
            'ъ' => '',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',
        );

        $output = str_replace(
            array_keys($table),
            array_values($table),$string
        );

        $output = strtolower(trim($output));

        $output = preg_replace("/\s+/", "_", $output);

        $output = preg_replace("[^A-Za-z0-9_\-]", "", $output);

        return $output;

    }

    // Функция получения уникального псевдонима

    public static function getUniqueAlias($alias, $table_name, $id=null)
    {
        $registry = Registry::__instance();
        $n = 1;
        $alias2 = $alias;
        if(isset($id))
        {
            $query = "select count(*) from $table_name where alias=:alias and id <> $id";
        }
        else
        {
            $query = "select count(*) from $table_name where alias=:alias";
        }
        $sql = $registry->db->prepare($query);
        $sql->bindParam(':alias',$alias2);
        $sql->execute();
        $num = $sql->fetchColumn();
        while($num > 0)
        {
            $n++;
            $alias2 = $alias.$n;
            $sql->execute();
            $num = $sql->fetchColumn();
        }

        return $alias2;
    }

  /**
   * генерация thumbnails

   *
   * @param string $src имя исходного файла
   * @param string $dest имя генерируемого файла
   * @param integer $width ширина генерируемого изображения, в пикселях
   * @param integer $height высота генерируемого изображения, в пикселях
   * @param boolean $orig_ratio оставлять измененный размер изображений пропорционаллным исходному
   * @param <type> $rgb цвет фона, по умолчанию - белый
   * @param integer $quality качество генерируемого JPEG, по умолчанию - максимальное (100)
   * @return boolean 
   */
  public static function img_resize($src, $dest, $width, $height, $orig_ratio=true, $rgb=0xFFFFFF, $quality=100)
  {
      if (!is_file($src)) return false;

      $size = getimagesize($src);

      if ($size === false) return false;

      if($width == 0) $width = $size[0];
      if($height == 0) $height = $size[1];

      // Определяем исходный формат по MIME-информации, предоставленной
      // функцией getimagesize, и выбираем соответствующую формату
      // imagecreatefrom-функцию.
      $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
      $icfunc = "imagecreatefrom" . $format;
      if (!function_exists($icfunc)) return false;

      $x_ratio = $width / $size[0];
      $y_ratio = $height / $size[1];

      $ratio       = min($x_ratio, $y_ratio);
      $use_x_ratio = ($x_ratio == $ratio);

      $new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
      $new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
      $new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width) / 2);
      $new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

        try
        {
            $isrc = $icfunc($src);
        }
        catch(Exception $e)
        {
            return;
        }

      if(!$orig_ratio)
      {
          $idest = imagecreatetruecolor($width, $height);
          imagefill($idest, 0, 0, $rgb);

          $sourceWidth = $size[0];
          $sourceHeight = $size[1];

          $X = 0;
          $Y = 0;

          $W = $sourceWidth;
          $H = $sourceHeight;


          $Ww = $W / $width;
          $Hh = $H / $height;
          if ( $Ww > $Hh )
          {
              $W = floor($width * $Hh);
              $X = floor(($sourceWidth -  $W)*0.5);
          } else
          {
              $H = floor($height * $Ww);
              $Y = floor(($sourceHeight - $H)*0.5);
          }

          imagecopyresampled(
                  $idest, $isrc,
                  0, 0,
                  $X, $Y,
                  $width, $height,
                  $W, $H
          );
      }
      else
      {
          $idest = imagecreatetruecolor($new_width , $new_height);
          $new_left = 0;
          $new_top = 0;
          imagefill($idest, 0, 0, $rgb);
          imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0,
                  $new_width, $new_height, $size[0], $size[1]);
      }

      imagejpeg($idest, $dest, $quality);

      imagedestroy($isrc);
      imagedestroy($idest);

      return true;

  }

    /**
     * Наложение водяного знака на изображение
     *
     * @param string $watermark_file файл с изображением водяного знака
     * @param string $image_file файл изображения
     * @param string $out_file файл изображения с наложенным водяным знаком
     */
    public static function img_watermark($watermark_file,$image_file,$out_file)
    {
        $znak_hw = getimagesize($watermark_file);
        $foto_hw = getimagesize($image_file);

        $znak = imagecreatefrompng  ($watermark_file);
        $foto = imagecreatefromjpeg ($image_file);

        //echo $znak_hw[0].'x'.$znak_hw[1];return;

        imagecopy ($foto,
            $znak,
//            $foto_hw[0] - $znak_hw[0],
//            $foto_hw[1] - $znak_hw[1],
            floor($foto_hw[0] / 2) - floor($znak_hw[0] / 2),
            floor($foto_hw[1] / 2) - floor($znak_hw[1] / 2),
            0,
            0,
            $znak_hw[0],
            $znak_hw[1]);

//        switch ($foto_hw[2])
//        {
//            case 1:
//                imageGIF($foto,$out_file);
//                break;
//            case 2:
//                imageJPEG($foto,$out_file);
//                break;
//            case 3:
//                imagePNG($foto,$out_file);
//                break;
//        }

        imagejpeg ($foto, $out_file, "100");

        imagedestroy ($znak);
        imagedestroy ($foto);

    }

    public static function createSecurimage($name=null)
    {
        include_once SITE_PATH.'cms'.DS.'lib'.DS.'securimage'.DS.'securimage.php';

        return new Securimage($name);
    }

}



?>
