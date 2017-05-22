<?php

/**
 * Copyright 2017 Csaba Fekete (feketecsaba@gmail.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

class ExcessConverter
{
    const RETURN_ARRAY_DEC;
    const RETURN_ARRAY_HEX;
    const RETURN_ASCII;
    
    public static function convert($num, $return_type = self::RETURN_ARRAY_HEX)
    {
        if(!in_array($return_type, array(self::RETURN_ARRAY_DEC, self::RETURN_ARRAY_HEX, self::RETURN_ASCII)))
        {
            throw new \Exception("Wrong return type specified");
        }
        
        if(bccomp(-1.70141183e+38, $num) == 1 || bccomp(1.70141183e+38 , $num) == -1)
        {
            throw new \Exception("Number is out of range");
        }
                
        $negative = $num < 0;
        $num = abs($num);

        $whole = floor($num);
        $fraction = $num - floor($num);

        $fbin = self::_fdecbin($fraction);

        $binary = decbin($whole) . "." . $fbin;

        $exponent = strpos($binary, ".");
        $exponent = ((int)$exponent) + 128;

        $mantissa = str_pad(decbin($whole) . $fbin, 32, "0");
        $mantissa[0] = $negative ? "1" : "0";

        $m4 = substr($mantissa, 0, 8);
        $m3 = substr($mantissa, 8, 8);
        $m2 = substr($mantissa, 16, 8);
        $m1 = substr($mantissa, 24, 8);

        switch($return_type)
        {
            case self::RETURN_ARRAY_DEC:
                return [$exponent, bindec($m4), bindec($m3), bindec($m2), bindec($m1)];        
                break;
        
            case self::RETURN_ASCII:
                return chr($exponent).chr($m4).chr($m3).chr($m2).chr($m1);
                break;
                
            case self::RETURN_ARRAY_HEX:
            default:
                return [dechex($exponent), base_convert($m4, 2, 16), base_convert($m3, 2, 16), base_convert($m2, 2, 16), base_convert($m1, 2, 16)];        
                break;
        }
        
        
        
    }

    private static function _fdecbin($fraction)
    {
        if($fraction == 0)
        {
            return "0";
        }

        $precision = 32;
        bcscale($precision);
        $result="";
        $exp = -1;
        while($fraction>0 && (-$exp <= $precision))
        {
            $div = floor(bcdiv($fraction, bcpow(2, $exp)));
            $mod = bcsub($fraction, bcmul($div, bcpow(2, $exp)));
            $result.=$div;
            $fraction = $mod;
            $exp--;
        }

        return $result;
    }
}
