<?php

namespace app\models;

use yii\base\InvalidCallException;
use yii\base\InvalidParamException;


/**
 * Class Stateflag
 * @package app\models
 *
 * Класс флагов для обращения и ответа
 *
 */
class Stateflag {
    const STATE_APPEAL_NEW = 0;      // Новое сообщение
    const STATE_APPEAL_DELETED = 1;  // Удаленное
    const STATE_APPEAL_PUBLIC = 2;   // Видимое всем
    const STATE_APPEAL_PRIVATE = 3;  // Видимо только внутри системы

    const STATE_ANSWER_NEW = 0;        // Новый ответ
    const STATE_ANSWER_APPROVED = 1;  // Ответ, проверен контролером
    const STATE_ANSWER_MODERATED = 2;  // Ответ, проверен модератором
    const STATE_ANSWER_PUBLISHED = 3;  // Принят
    const STATE_ANSWER_TOFIX = 4;      // На исправлении
    const STATE_ANSWER_REVISED = 5;    // Доработан

    public static $aAppealFlags = [
        self::STATE_APPEAL_NEW => [
            'title' => 'Новое обращение',
            'stitle' => 'Новое',
            'glyth' => 'envelope',
            'color' => '#ff0000',
            'hint' => 'Новое обращение',
        ],
        self::STATE_APPEAL_DELETED => [
            'title' => 'Удаленное обращение',
            'stitle' => 'Удалено',
            'glyth' => 'trash',
            'color' => '#cccccc',
            'hint' => 'Удаленное обращение',
        ],
        self::STATE_APPEAL_PUBLIC => [
            'title' => 'Поручение',
            'stitle' => 'Поруч.',
            'glyth' => 'comment',
            'color' => '#ff4500',
            'hint' => 'Видимо всем',
        ],
        self::STATE_APPEAL_PRIVATE => [
            'title' => 'Внутреннее поручение',
            'stitle' => 'ВП',
            'glyth' => 'list-alt',
            'color' => '#ff4500',
            'hint' => 'Видимо только внутри системы',
        ],
    ];

    public static $aAnswerFlags = [
        self::STATE_ANSWER_NEW => [
            'title' => 'Новый ответ',
            'stitle' => 'Новый',
            'glyth' => 'info-sign',
            'color' => '#ff1493',
            'hint' => 'Новый ответ',
        ],
        self::STATE_ANSWER_APPROVED => [
            'title' => 'Проверен контролером',
            'stitle' => 'Контр.',
            'glyth' => 'question-sign',
            'color' => '#ff1493',
            'hint' => 'Проверен контролером',
        ],
        self::STATE_ANSWER_MODERATED => [
            'title' => 'Проверен модератором',
            'stitle' => 'Модерат.',
            'glyth' => 'plus-sign',
            'color' => '#ff1493',
            'hint' => 'Проверен модератором',
        ],
        self::STATE_ANSWER_PUBLISHED => [
            'title' => 'Принят',
            'stitle' => 'Принят',
            'glyth' => 'ok-sign',
            'color' => '#2e8b57',
            'hint' => 'Принят',
        ],
        self::STATE_ANSWER_TOFIX => [
            'title' => 'На доработке',
            'stitle' => 'Доработ',
            'glyth' => 'minus-sign',
            'color' => '#ff1493',
            'hint' => 'На доработке',
        ],
        self::STATE_ANSWER_REVISED => [
            'title' => 'Исправлен',
            'stitle' => 'Исправл.',
            'glyth' => 'remove-sign',
            'color' => '#ff1493',
            'hint' => 'Исправлен',
        ],
    ];

//    /**
//     * Получение всех данных для флагов обращения
//     *
//     * @return array
//     */
//    public static function getAppealFlags() {
//        return self::$aAppealFlags;
//    }
//
//    /**
//     * Получение данных для флага обращения
//     *
//     * @return array
//     */
//    public static function getAppealFlag($nFlag = -1) {
//        return isset(self::$aAppealFlags[$nFlag]) ? self::$aAppealFlags[$nFlag] : [];
//    }
//
//    /**
//     * Получение всех данных для флагов ответа
//     *
//     * @return array
//     */
//    public static function getAnswerFlags() {
//        return self::$aAnswerFlags;
//    }
//
//    /**
//     * Получение данных для флага ответа
//     *
//     * @return array
//     */
//    public static function getAnswerFlag($nFlag = -1) {
//        return isset(self::$aAnswerFlags[$nFlag]) ? self::$aAnswerFlags[$nFlag] : [];
//    }

    /**
     *
     * Создаем набор функций для получения данных по флагам:
     *
     * getAnswerFlags - все данные по флагам ответа
     * getAppealFlags - все данные по флагам обращения
     * getAnswerFlag(id) - все данные по флагу id ответа
     * getAppealFlag(id) - все данные по флагу id обращения
     * getAnswerTitle(id) - title флага id ответа
     * getAppealTitle(id) - title флага id обращения
     * getAnswerSTitle(id) - stitle флага id ответа
     * getAppealSTitle(id) - stitle флага id обращения
     * getAnswerColor(id) - color флага id ответа
     * getAppealColor(id) - color флага id обращения
     * getAnswerGlyth(id) - glyth флага id ответа
     * getAppealGlyth(id) - glyth флага id обращения
     * getAnswerHint(id) - hint флага id ответа
     * getAppealHint(id) - hint флага id обращения
     *
     * @param $sFunc string    название несуществующей функции
     * @param $idFlag integer  флаг, для которого будем выгребать данные
     * @return array|string|null
     */
    public static function __callStatic($sFunc, $idFlag = -1) {
        $aRet = null;
        if( is_array($idFlag) ) {
            $idFlag = empty($idFlag) ? -1 : $idFlag[0];
        }

        // разгребаем название функции
        if( preg_match('/^get(Answer|Appeal)(.*)$/i', $sFunc, $aParts) ) {

            // определяем, чьи флаги брать
            if( strtolower($aParts[1]) == 'answer' ) {
                $a = self::$aAnswerFlags;
            }
            else { // if( strtolower($aParts[1]) == 'appeal' ) {
                $a = self::$aAppealFlags;
            }

            // определяем, что выдавать
            if( strtolower($aParts[2]) == 'flags' ) {
                $aRet = $a;
            }
            else if( strtolower($aParts[2]) == 'flag' ) {
                $aRet = isset($a[$idFlag]) ? $a[$idFlag] : [];
            }
            else if( isset($a[$idFlag]) && isset($a[$idFlag][strtolower($aParts[2])]) ) {
                $aRet = $a[$idFlag][strtolower($aParts[2])];
            }
            else {
                throw new InvalidParamException ('Error not found '.$aParts[1].' data to use in flag ('.$idFlag.') data ' . $aParts[2]);
            }
        }
        else {
            throw new InvalidCallException('Error not found function to use in flag data');
        }
        return $aRet;
    }


}