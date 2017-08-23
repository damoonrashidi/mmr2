<?php
class Validate {

  const EMAIL = "/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix";
  const NUMBER = "is_numeric";
  public static function positive($value) {
    return is_numeric($value) && $value > 0;
  }
  public static function negative($value) {
    return is_numeric($value) && $value < 0;
  }
  public static function required($value) {
    return !empty($value);
  }

}
