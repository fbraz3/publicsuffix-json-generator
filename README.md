# publicsuffix-json-generator

A tool to generate a machine-readable [Public Suffix List](https://publicsuffix.org/) in JSON format, powering [publicsuffix-json](https://github.com/fbraz3/publicsuffix-json).

## Objective

This project automates the process of fetching, parsing, and converting the Public Suffix List into a compact, easy-to-use JSON file. The generated JSON can be consumed by applications in various programming languages to efficiently determine domain suffixes.

## Features

- Fetches the latest Public Suffix List
- Converts the list to a normalized JSON structure
- Designed for integration in CI/CD pipelines
- Output compatible with multiple languages

## Usage

### 1. Generate the JSON

Clone this repository and run the generator:

```bash
git clone https://github.com/fbraz3/publicsuffix-json-generator.git
cd publicsuffix-json-generator
php generate.php > publicsuffix.json
```

2. Use the JSON in Your Project

### php

```php
$data = json_decode(file_get_contents('publicsuffix.json'), true);
$suffixes = $data['rules'];
// Example: check if 'co.uk' is a public suffix
if (in_array('co.uk', $suffixes)) {
    // logic here
}
```

### javascript (nodejs)

```javascript
const data = require('./publicsuffix.json');
const suffixes = data.rules;
// Example: check if 'com' is a public suffix
if (suffixes.includes('com')) {
    // logic here
}
```

### python

``` python
const data = require('./publicsuffix.json');
const suffixes = data.rules;
// Example: check if 'com' is a public suffix
if (suffixes.includes('com')) {
    // logic here
}
```

### go

```go
import (
    "encoding/json"
    "os"
)

type PSL struct {
    Rules []string `json:"rules"`
}

file, _ := os.Open("publicsuffix.json")
defer file.Close()
var psl PSL
json.NewDecoder(file).Decode(&psl)
// Example: check if "net" is a public suffix
for _, s := range psl.Rules {
    if s == "net" {
        // logic here
    }
}
```

## License

MIT

## See Also

- [publicsuffix-json](https://github.com/fbraz3/publicsuffix-json)
- [Public Suffix List](https://publicsuffix.org/)