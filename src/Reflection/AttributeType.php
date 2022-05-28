<?php

declare(strict_types=1);

namespace NGSOFT\Reflection;

use Attribute,
    NGSOFT\Enums\Enum;

/**
 * Attribute Target Enum
 *
 * @method static static ATTRIBUTE_CLASS()
 * @method static static ATTRIBUTE_CLASS_CONSTANT()
 * @method static static ATTRIBUTE_FUNCTION()
 * @method static static ATTRIBUTE_METHOD()
 * @method static static ATTRIBUTE_PARAMETER()
 * @method static static ATTRIBUTE_PROPERTY()
 * @method static static ATTRIBUTE_ALL()
 */
class AttributeType extends Enum {

    public const ATTRIBUTE_CLASS = Attribute::TARGET_CLASS;
    public const ATTRIBUTE_CLASS_CONSTANT = Attribute::TARGET_CLASS_CONSTANT;
    public const ATTRIBUTE_PROPERTY = Attribute::TARGET_PROPERTY;
    public const ATTRIBUTE_METHOD = Attribute::TARGET_METHOD;
    public const ATTRIBUTE_FUNCTION = Attribute::TARGET_FUNCTION;
    public const ATTRIBUTE_PARAMETER = Attribute::TARGET_PARAMETER;
    public const ATTRIBUTE_ALL = Attribute::TARGET_ALL;

}
