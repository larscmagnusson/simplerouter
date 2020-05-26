<?php

namespace SimpleRouter;

class SimpleRouter
{
  private $basepath_array;
  private $current_path_array;
  private $matching_base;

  public static function setup($base)
  {
    return new SimpleRouter($base);
  }

  /**
   * [x,y] matches [x,y] and [x,y,z] but not [x,k,z]
   */
  private static function partialMatchingArrays($arr1, $arr2)
  {
    $matching = true;
    var_dump($arr1);
    foreach ($arr1 as $index => $segment) {
      // echo $current_path_array[$index] . " --- " . $segment . PHP_EOL;
      if ($arr2[$index] != $segment) {
        $matching = false;
        break;
      }
    }
    return $matching;
  }

  public static function params($key = null)
  {
    if(!$key) {
      return $_REQUEST;
    }

    if (isset($_REQUEST[$key])) {
      return $_REQUEST[$key];
    }
  }

  public static function json($data, $status_code = 200)
  {
    if(!http_response_code() || http_response_code() == 200) {
      //header('Content-Type: application/json');
      
      http_response_code($status_code);
      echo json_encode($data);
      exit;
    }
  }

  public function __construct($base)
  {
    if ($base[0] !== '/') {
      throw new \Exception('$base must start with /');
    }

    $this->basepath_array = array_values(array_filter(explode('/', $base)));
    $current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $this->current_path_array = array_values(array_filter(explode('/', $current_path)));

    $this->matching_base = self::partialMatchingArrays($this->basepath_array, $this->current_path_array);
  }

  public function get($path, $callbacks = [])
  {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      return $this->req($path, $callbacks);
    } else {
      return $this;
    }
  }

  public function post($path, $callbacks = [])
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      return $this->req($path, $callbacks);
    } else {
      return $this;
    }
  }

  private function req($path, $callbacks = [])
  {
    // Not matching, do nothing..
    if (!$this->matching_base) {
      return $this;
    }

    $the_rest = array_values(array_slice($this->current_path_array, count($this->basepath_array)));
    $given_path_array = array_values(array_filter(explode('/', $path)));
   
    if (self::partialMatchingArrays($given_path_array, $the_rest)) {
      $arguments = array_values(array_slice($the_rest, count($given_path_array)));

      if (is_callable($callbacks)) {
        call_user_func_array($callbacks, $arguments);
      } elseif (is_array($callbacks)) {

        $called_someone = false;

        // Find best match
        foreach ($callbacks as $callback) {
          // how many arguments is needed? does it match nr of arguments in path?
          if ((new \ReflectionFunction($callback))->getNumberOfParameters() == count($arguments)) {
            call_user_func_array($callback, $arguments);
            $called_someone = true;
            break;
          }
        }

        // fallback to call first callback
        if (!$called_someone) {
          call_user_func_array($callbacks[0], $arguments);
        }
      } else {
        throw new \Exception("callback is invalid. must be callable or array of callables");
      }
    }

    return $this;
  }
}
