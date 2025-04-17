<?php

declare(strict_types=1);

namespace Modules\Xot\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Xot\Models\Widget;

/**
 * Undocumented class.
 */
class WidgetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Widget::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->randomNumber(5, false),
            'post_type' => $this->faker->word,
            'post_id' => $this->faker->randomNumber(5, false),
            'title' => $this->faker->sentence,
            'blade' => $this->faker->word,
            'pos' => $this->faker->randomNumber(5, false),
            'model' => $this->faker->word,
            'limit' => $this->faker->randomNumber(5, false),
            'order_by' => $this->faker->word,
            'image_src' => $this->faker->word,
            'layout_position' => $this->faker->word,
        ];
    }
}
