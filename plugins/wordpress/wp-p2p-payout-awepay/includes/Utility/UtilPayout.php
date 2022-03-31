<?php
/**
 * 
 */
namespace AwepayPayout\Utility;


class UtilPayout 
{

   public static function getRandomStrUsingSha1Time($length_of_string) {

        return substr(sha1(time()), 0, $length_of_string);

   }
   
  public static function getUniqueNumber()
  {
        $timestamp = microtime(true)*10000;		
		return $timestamp.rand(1,10);
  }
   
   public static function getRandomStrUsingBin2hexRandomByte($length_of_string) {

         return substr(bin2hex(random_bytes($length_of_string)),0, $length_of_string);
   }

   public static function getRandomStrUsingMd5Time($length_of_string) {
    return substr(md5(time()), 0, $length_of_string);
   }
	
}