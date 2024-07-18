# PHP OpenAI Tool Attribute

This project demonstrates the use of PHP's reflection API to generate OpenAI API-compatible "tool use" (or "function calling") structures based on a simple PHP attribute. The example provided shows how to annotate functions with custom attributes and then dynamically generate a schema for those functions.

## Features

- Define functions with custom attributes.
- Use PHP reflection to inspect functions and their parameters.
- Generate JSON schema compatible with OpenAI's function calling structures.

## Installation

Ensure you have PHP installed on your system. Then, follow these steps to set up the project.

### Clone the Repository

```sh
git clone <your-github-repo-url>
cd <your-repo-directory>
```

### Dependencies

This project does not require any external dependencies beyond PHP itself.

## Usage

To generate the tools schema, simply run the `tool_schema.php` file:

```sh
php tool_schema.php
```

## How It Works

### Attribute Definition

The `OpenAiTool` attribute is defined as follows:

```php
#[Attribute(Attribute::TARGET_FUNCTION)]
class OpenAiTool
{
    public function __construct(
        public string $description
    ) {}
}
```

This attribute can be applied to any function to provide a description.

### Example Function

An example function annotated with the `OpenAiTool` attribute:

```php

Enum TemperatureUnit: string {
    case CELSIUS = 'celsius';
    case FAHRENHEIT = 'fahrenheit';
}

#[OpenAiTool('Get the current weather in a given location')]
function getCurrentWeather(string $location, TemperatureUnit $unit = TemperatureUnit::CELSIUS): string
{
    // implementation
}
```

### Schema Generation

The `generateToolsSchema` function uses PHP's reflection API to inspect all user-defined functions, check for the `OpenAiTool` attribute, and generate a JSON schema based on the function's parameters.

### Example output

```json
[
    {
        "type": "function",
        "function": {
            "name": "getCurrentWeather",
            "description": "Get the current weather in a given location",
            "parameters": {
                "type": "object",
                "properties": {
                    "location": {
                        "type": "string"
                    },
                    "unit": {
                        "type": "string",
                        "enum": [
                            "celsius",
                            "fahrenheit"
                        ],
                        "default": "celsius"
                    }
                },
                "required": [
                    "location"
                ]
            }
        }
    }
]
```

## License

This project is licensed under the MIT License.
