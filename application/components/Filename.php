<?php
/**
 * Created by PhpStorm.
 * User: xiehuanjin
 * Date: 2019/2/27
 * Time: 11:03
 */

namespace app\components;


class Filename
{
    protected $translator;

    public function __construct()
    {
        $this->translator = new TranslateByYoudao();
    }

    public function generateUrl($title)
    {
        preg_match('/^\d{4}-\d{2}-\d{2}-/', $title, $match);
        $datePre = '';
        if (!empty($match[0])) {
            $datePre = $match[0];
            $title = str_replace($datePre, '', $title);
        }

        if ($titleEn = $this->translator->zh2en($title)) {
            $titleEn = strtolower($titleEn);
            $titleEn = str_replace(['&','【','】','[',']'], ' ', $titleEn);
            $titleEn = str_replace("'", ' ', $titleEn);
            $titleEn = str_replace(",", ' ', $titleEn);
            $titleEn = preg_replace('/\s+/', ' ', $titleEn);
            $titleEn = str_replace(' ', '-', trim($titleEn));
            return $datePre . $titleEn;
        }

        return $title;
    }
}