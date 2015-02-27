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
    'us_regtime' => $faker->date . ' ' . $faker->time,
    'us_workposition' => $faker->sentence(8),

    'us_password_hash' => Yii::$app->getSecurity()->generatePasswordHash('' . $index . $index . $index . $index),
    'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
];
