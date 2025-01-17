<?php

use Faker\Generator as Faker;
use App\Task;
use App\User;
use App\TaskStatus;

$factory->define(Task::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence(),
        'description' => $faker->text(1000),
        'completed' => $faker->boolean(20),
        'due_at' => $faker->dateTimeBetween('+0 days', '+23 days'),
        'assignee_id' => User::all()->random()->id,
        'status_id' => TaskStatus::all()->random()->id
    ];
});
