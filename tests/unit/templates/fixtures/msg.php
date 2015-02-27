<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'msg_pers_name' => $faker->firstName,
    'msg_pers_secname' => $faker->firstName,
    'msg_pers_lastname' => $faker->lastName,
    'msg_pers_phone' => '+7(' . $faker->numerify('###') . ')' . $faker->numerify('###') . '-' . $faker->numerify('##') . '-' . $faker->numerify('##'), // phoneNumber,
    'msg_pers_email' => $faker->freeEmail,
    'city' => $faker->city,
    'msg_pers_org' => $faker->numerify('ГОУ СОШ № ###'),
    'msg_pers_region' => $faker->randomDigitNotNull,
    'msg_pers_text' => $faker->paragraph(10),
    'msg_answer' => $faker->paragraph(3),
    'msg_createtime' => $faker->date . ' ' . $faker->time,
    'msg_active' => 1,

    'password' => Yii::$app->getSecurity()->generatePasswordHash($index . $index . $index . $index),
    'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
//     'intro' => $faker->sentence(7, true),  // generate a sentence with 7 words
];
