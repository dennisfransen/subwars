<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RuleTemplate
 *
 * @property int $id
 * @property string $rules
 * @method static \Database\Factories\RuleTemplateFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|RuleTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RuleTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RuleTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|RuleTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuleTemplate whereRules($value)
 * @mixin \Eloquent
 * @property string $title
 * @method static \Illuminate\Database\Eloquent\Builder|RuleTemplate whereTitle($value)
 */
class RuleTemplate extends Model
{
    use HasFactory;

    public $timestamps = false;
}
