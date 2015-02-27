<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'us_name' => $faker->firstName,
    'us_secondname' => $faker->firstName,
    'us_lastname' => $faker->lastName,
    'us_login' => $faker->word . $index, 
    'us_email' => $faker->freeEmail,
    'city' => $faker->city,
    'us_workposition' => $faker->paragraph(8),

    'us_password_hash' => Yii::$app->getSecurity()->generatePasswordHash('' . $index . $index . $index . $index),
    'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
];
