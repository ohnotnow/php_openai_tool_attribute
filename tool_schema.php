<?php

#[Attribute(Attribute::TARGET_FUNCTION)]
class OpenAiTool
{
    public function __construct(
        public string $description
    ) {}
}

Enum TemperatureUnit: string {
    case CELSIUS = 'celsius';
    case FAHRENHEIT = 'fahrenheit';
}

#[OpenAiTool('Get the current weather in a given location')]
function getCurrentWeather(string $location, TemperatureUnit $unit = TemperatureUnit::CELSIUS): string
{
    // implementation
}

function generateToolsSchema(): array
{
    $tools = [];

    $functions = get_defined_functions()['user'];
    foreach ($functions as $functionName) {
        $reflectionFunction = new ReflectionFunction($functionName);
        $attributes = $reflectionFunction->getAttributes(OpenAiTool::class);

        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();

            $parametersSchema = [
                'type' => 'object',
                'properties' => [],
                'required' => []
            ];

            foreach ($reflectionFunction->getParameters() as $param) {
                $paramName = $param->getName();
                $paramType = $param->hasType() ? $param->getType()->getName() : 'string';
                $paramDefault = $param->isOptional() ? $param->getDefaultValue() : null;

                $propertySchema = ['type' => $paramType];

                if (enum_exists($paramType)) {
                    $enumCases = array_map(fn($case) => $case->value, $paramType::cases());
                    $propertySchema['type'] = 'string';
                    $propertySchema['enum'] = $enumCases;
                }

                if ($paramDefault !== null) {
                    $propertySchema['default'] = $paramDefault instanceof \BackedEnum ? $paramDefault->value : $paramDefault;
                } else {
                    $parametersSchema['required'][] = $paramName;
                }

                $parametersSchema['properties'][$paramName] = $propertySchema;
            }

            $tools[] = [
                'type' => 'function',
                'function' => [
                    'name' => $reflectionFunction->getName(),
                    'description' => $instance->description,
                    'parameters' => $parametersSchema
                ]
            ];
        }
    }

    return $tools;
}

$toolsSchema = generateToolsSchema();
print(json_encode($toolsSchema, JSON_PRETTY_PRINT));
?>
