<?php
$file = 'settings.php'; // название файла с настройками

function calcDepth($array)  { // рекурсивный рассчет глубины массива
    $max_depth = 1;

    foreach ($array as $value) 
    {
      if (is_array($value)) 
      {
        $depth = calcDepth($value) + 1;
        if ($depth > $max_depth) 
        {
          $max_depth = $depth;
        }
      }
    }

    return $max_depth;
}

function checkDepth($depth, $count) { // сравнение, совпадает ли глубина массива с количеством указаннхы элементов в составном ключе
    if ($depth !== $count)
    {
      throw new Exception('В настройках нет такой опции!');
    }
}


function checkExist($x, $y) {
    if (!isset($x[$y]))
      {
        //echo 'Нет такого значения: '.$y."<br/>";
        throw new Exception('В настройках нет такой опции!');
      }
      else
      {
        //echo 'значение: '.$y."<br/>";
        return $x[$y];
      }
}


function func($array, $elements, $x) // рекурсивная функция, принимает исходный массив, массив с "путем" и индекс для пути
{
    foreach ($array as $item) // цикл для перебора всех элементов
    {
        if (is_array($item)) // проверка, является ли элемент массивом
        {
          checkExist($array, $elements[$x]); // проверка каждой части составного ключа
          $x++; // для перехода к следующему элементу в массиве с путем
          $res = func($item, $elements, $x);
          if ($res !== NULL) { // чтобы сработал return
            return $res;
          }
        }
        else // конечный элемент, который не является массивом
        {
          if (array_search($item, $array) == $elements[$x]) // проверка, совпадает ли указанный ключ
          {
            return $item; // возвращение значения
          }
          else 
          {
            checkExist($array, $elements[$x]); // проверка последнего элемента
          }
        }
    }
}


function config($optionName, $defaultValue = null)
{
  if ($defaultValue !== null) // если установлено значение по умолчанию, оно же возвращается
  {
    return $defaultValue;
  }

  global $file; // обращение к глобальной переменной
  $array = include $file; // получение массива из файла с настройками

  if (strpos($optionName, '.') == true) // проверка, есть ли в строке точка, т.е. составной ли ключ
  {
    $elements = explode(".", $optionName); // разбивка в массив ключей, т.к. они указаны в неподходящем формате - через точку
    //echo 'el1: '.$elements[0]."<br/>";
    //echo 'el2: '.$elements[1]."<br/>";

    $depth = calcDepth($array[$elements[0]])+1; // единица добавляется, потому что проверка массива идет за исключением верхнего элемента
    $count = count($elements); // количество элементов в одномерном массиве
    //echo 'depth: '.$depth."<br/>";
    //echo 'count: '.$count."<br/>";

    try // проверка, что глубина массива совпадает с количеством указанных элементов в составном ключе
    {
      checkDepth($depth, $count);
    }
    catch (Exception $e1) 
    {
      echo 'Выброшено исключение: ',  $e1->getMessage(), "\n";
      return;
    }

    try 
    {
      $final = func(checkExist($array, $elements[0]), $elements, 1); // вызов рекурсивной функции
      return $final;
    }
    catch (Exception $e2) 
    {
      echo 'Выброшено исключение: ',  $e2->getMessage(), "\n";
      return;
    }
  }
  else // если в строке нет точки, т.е. несоставной ключ
  {
      try 
      {
        return checkExist($array, $optionName); // вызывается элемент, где название ключа совпадает с $optionName с проверкой на существование опции
      } 
      catch (Exception $e2) 
      {
        echo 'Выброшено исключение: ',  $e2->getMessage(), "\n";
        return;
      }
  }

}

// вызовы функции
echo config("site_url")."<br/>"; // поиск по ключу, http://mysite.ru
echo config("db.name")."<br/>"; // поиск по составному ключу с двумя значениями, my_database
echo config("app.services.resizer.fallback_format")."<br/>"; // поиск по составному ключу, где более двух значений, jpeg
echo config("db.host", "localhost")."<br/>"; // поиск со значением по умолчанию, localhost
echo config("db2")."<br/>"; // поиск по несуществующему ключу, исключение
echo config("db.host2")."<br/>"; // поиск по несуществующему составному ключу, исключение
echo config("app.services.fallback_format2.prefer_format")."<br/>"; // поиск по несуществующему составному ключу, где более двух значений, исключение

?>