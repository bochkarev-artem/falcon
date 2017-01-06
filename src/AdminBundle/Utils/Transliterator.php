<?php
namespace AdminBundle\Utils;

use Gedmo\Sluggable\Util\Urlizer;

class Transliterator extends Urlizer
{
    private static $table =  array(
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
        'й' => 'y',
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
        'ц' => 'ts',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'sh',
        'ь' => '',
        'ы' => 'yi',
        'ъ' => "",
        'э' => 'e',
        'ю' => 'yu',
        'я' => 'ya'
    );

    public static function transliterate($text, $separator = '-')
    {
        $text = mb_strtolower($text);
        $text = strtr($text, self::$table);
        if (preg_match('/[\x80-\xff]/', $text) && self::validUtf8($text)) {
            $text = self::utf8ToAscii($text);
        }

        return self::urlize($text, $separator);
    }
}