<?php
  $text = "";
  if(isset($_REQUEST["r"])) {
    $text = $_REQUEST["r"];
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Search engine</title>
  </head>
  <body>
  <h1>Search engine</h1>
  <hr>
  <form action="">
    Text: <input type="text" name="r" size="50" value="<?php echo "$text" ?>"> <input type="submit" value="Search" />
  </form>
  <a href="index.html">Index</a>
  <hr>
  <?php
    if(strlen($text) > 0) {
      // Si verifica la presenza della parola "promo"
      // per effettuare la ricerca solo sulle promozioni
      $pos = strpos($text, "promo");
      $solo_promozioni = false;
      if($pos !== false) {
        $solo_promozioni = true;
        if($pos > 0) {
          $text = substr($text, 0, $pos - 1);
        }
        else {
          $space = strpos($text, ' ');
          if($space !== false) {
            $text = substr($text, $space + 1);
          }
          else {
            $text = "*";
          }
        }
      } 
      
      $files = find($text, "content/promo", true);
      $countPromo = count($files);
      if(!$solo_promozioni) {
        $files_prodotti = find($text, "content/products", true);
        $files = array_merge($files, $files_prodotti);
      }
      buildResult($files, 5, $countPromo);
    }
  ?>
  </body>
</html>

<?php
  function getText($file, $breakLine = "")
  {
    $result = "";
    $handle_file = fopen($file, "r");
    if ($handle_file) {
      while(!feof($handle_file)) {
        $linea = fgets($handle_file, 255);
        $result = $result . $linea . $breakLine;
      }
      fclose($handle_file);
    }
    return $result;
  }
  
  function getHTMLContent($file)
  {
    $result = "";
    $handle_file = fopen($file, "r");
    if ($handle_file) {
      while(!feof($handle_file)) {
        $linea = fgets($handle_file, 255);
        $result = $result . $linea;
      }
      fclose($handle_file);
    }
    return $result;
  }
  
  function contains($file, $text)
  {
    $arrayOfWord = explode(' ', $text);
    $arrayOfToken = array();
    $i = 0;
    foreach($arrayOfWord as $word) {
      // Si tolgono parole con meno di 4 caratteri
      // spesso corrispondenti ad articoli o a preposizioni.
      if(strlen($word) < 4) continue;
      // Si toglie un carattere alla stringa di ricerca
      // per cercare singolare e plurale (es. sedi-a sedi-e).
      $token = substr($word, 0, strlen($word) - 1);
      // Per i plurali tipo pacchi, sacchi, ecc. va tolta anche l'h.
      $last_char = substr($token, strlen($token) - 1);
      if($last_char == 'H' || $last_char == 'h') {
        $token = substr($token, 0, strlen($token) - 1);
      }
      $arrayOfToken[$i] = $token;
      $i++;
    }
    if(count($arrayOfToken) == 0) return false;
    $result = false;
    $handle_file = fopen($file, "r");
    if ($handle_file) {
      while(!feof($handle_file)) {
        $linea = fgets($handle_file, 255);
        foreach($arrayOfToken as $token) {
          $pos = strpos($linea, $token);
          if($pos !== false) {
            $result = true;
            break;
          }
        }
        if($result) break;
      }
      fclose($handle_file);
    }
    return $result;
  }
  
  function find($text, $folder, $check_content = false)
  {
    $result = array();
    $i = 0;
    $handle_dir = opendir("./" . $folder . "/");
    if ($handle_dir) {
      while (false !== ($file = readdir($handle_dir))) {
        if ($file != "." && $file != "..") {
          $dot_pos = strpos($file, '.');
          if($dot_pos > 0) {
            $ext = substr($file, $dot_pos);
            if($ext == '.txt') {
              $file_name = substr($file, 0, $dot_pos);
              if($text == '*') {
                $result[$i] = $folder . "/" . $file_name;
                $i++;
              }
              else {
                $pos = strpos($file_name, $text);
                if($pos !== false) {
                  $result[$i] = $folder . "/" . $file_name;
                  $i++;
                }
                else
                if($check_content && contains($folder . "/" . $file, $text)) {
                  $result[$i] = $folder . "/" . $file_name;
                  $i++;
                }
              }
            }
          }
        }
      }
      closedir($handle_dir);
    }
    return $result;
  }
  
  function buildResult($files, $columns, $countPromo)
  {
    $count = count($files);
    $start = 0;
    while($count > $start) {
      buildRow($files, $start, $columns, $countPromo);
      $start += $columns;
    }
  }
  
  function buildRow($files, $start, $length, $countPromo)
  {
    $count = count($files);
    if($count <= $start) return false;
    $end = $start + $length;
    $diff = $end - $count;
    if($diff > 0) {
      $end = $end - $diff;
    }
    for($i = $start; $i < $end; $i++) {
      $file = $files[$i];
      echo "<img src=\"" . $file . ".jpg\" alt=\"" . $file . "\">";
      $pos = strpos($file, "promo");
      if($pos === false) {
        echo "<div>" . getText($file . ".txt") . "</div>";
      }
      else {
        echo "<div>" . getText($file . ".txt") . "*</div>";
      }
    }
    return true;
  }
?>