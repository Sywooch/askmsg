<?php
namespace app\models;

class Rolesimport {

    const ROLE_1 = 1; // Администраторы
    const ROLE_2 = 2; // Все пользователи (в том числе неавторизованные)
    const ROLE_3 = 3; // Изменение своего профайла. Управление кешем
    const ROLE_4 = 4; // Пользователи имеющие право голосовать за рейтинг
    const ROLE_5 = 5; // Пользователи имеющие право голосовать за авторитет
    const ROLE_6 = 6; // Редакторы
    const ROLE_7 = 7; // Редактор Юридической консультации
    const ROLE_8 = 8; // Редактор отдела лицензирования
    const ROLE_10 = 9; // просмотр структуры
    const ROLE_11 = 10; // Модераторы "ДОгМ"
    const ROLE_12 = 11; // Ответчики "ДОгМ"
    const ROLE_13 = 12; // Редактор сайта
    const ROLE_14 = 13; // Модератор сервиса "Сбор информации"
    const ROLE_15 = 14; // Панель управления

    const ROLE_GUEST = 0;
    const ROLE_ADMIN = 1;
    const ROLE_ALL_USERS = 2;
    const ROLE_CHANGE_PROFILE = 3;
    const ROLE_VOTE_RATING = 4;
    const ROLE_VOTE_AUTORITY = 5;
    const ROLE_EDITOR = 6;
    const ROLE_EDITOR_LOWER = 7;
    const ROLE_EDITOR_LICENSE = 8;
    const ROLE_SHOW_STRUCT = 9;
    const ROLE_MODERATE_DOGM = 10;
    const ROLE_ANSWER_DOGM = 11;
    const ROLE_EDITOR_SITE = 12;
    const ROLE_MODERATE_COLLECT_INFO = 13;
    const ROLE_ADMIN_PANEL = 14;

    static $roles = [
        self::ROLE_1 => 'Администраторы',
        self::ROLE_2 => 'Все пользователи (в том числе неавторизованные)',
        self::ROLE_3 => 'Изменение своего профайла. Управление кешем',
        self::ROLE_4 => 'Пользователи имеющие право голосовать за рейтинг',
        self::ROLE_5 => 'Пользователи имеющие право голосовать за авторитет',
        self::ROLE_6 => 'Редакторы',
        self::ROLE_7 => 'Редактор Юридической консультации',
        self::ROLE_8 => 'Редактор отдела лицензирования',
        self::ROLE_10 => 'просмотр структуры',
        self::ROLE_11 => 'Модераторы "ДОгМ"',
        self::ROLE_12 => 'Ответчики "ДОгМ"',
        self::ROLE_13 => 'Редактор сайта',
        self::ROLE_14 => 'Модератор сервиса "Сбор информации"',
        self::ROLE_15 => 'Панель управления',
    ];

    static public function getRoleName($id) {
        return self::$roles[$id];
    }

}
