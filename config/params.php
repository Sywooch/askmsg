<?php

return [
    'adminEmail' => 'devmosedu@yandex.ru',
    'supportEmail' => 'devmosedu@yandex.ru',
    'notifyEmail' => 'KozminVA@edu.mos.ru',
    'user.passwordResetTokenExpire' => '3600',
    'message.file.ext' => ['doc', 'docx', 'xls', 'xlsx', 'jpg', 'png', 'pdf', 'txt', 'zip'],
    'message.archiveperiod' => 365 * 1.5, // срок в днях, за который показывать обращения для пользователей, старше такого возраста - не показывать
    'message.file.maxsize' => 5000000,
    'message.file.newcount' => 1,
    'message.file.answercount' => 3,
    'message.file.uploaddir' => '@webroot/upload/files',
    'message.encode.key' => 'some0secret1text',
    'message.encode.method' => 'bf-cbc',
    'message.encode.iv' => '56897ngq',
    'import.host' => 'map.production.mskobr.ru',
    'tag.separator' => '|',
    'tag.addusertags' => false,
];
