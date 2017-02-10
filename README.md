# Content Stylizer - PHP

This library was written to support converting [text objects introduced in Schibsted Media Platform's article format version 5](https://github.schibsted.io/spt-mediaplatform/formatron/tree/master/article-v5#text) into HTML in PHP.

To use it you have to create Stylizer instance and define a list of supported tags in one of two ways. Then you can use `getHtml` method passing text and an array with markups and receiving HTML code as an output.

## Basic usage:

### Defining tags list (method 1):
```
$stylizer = new \ContentStylizer\Stylizer([
    [
        'beginning' => '<strong>',
        'end' => '</strong>',
        'type' => 'strong',
    ],
]);
```
### Defining tags list (method 2):
```
$stylizer = new \ContentStylizer\Stylizer();
$stylizer->addTag('strong', '<strong>', '</strong>');
```
### Converting text into HTML:
```
$text = 'Sample text';
$markups = [
    [
        'length' => 6,
        'offset' => 0,
        'type' => 'strong',
    ],
];

$html = $stylizer->getHtml($text, $markups);
```

## Parameters inside tags:

Both `beginning` and `end` attribute of any tag can be defined using string or anonymous function. The second way allows us to add some logic to HTML creation process and to use parameters passed in markup objects.

Let's define `link` tag:
```
$stylizer->addTag('link', function (\stdClass $params) {
    return '<a href="' . $params->uri . '" target="_blank">;
}, '</a>');
```
Then we can use `link` markup:
```
$html = $stylizer->getHtml('Our link', [
    [
        'length' => 4,
        'offset' => 4,
        'type' => 'link',
        'uri' => 'http://www.example.com',
    ],
]);
```

Note that `$params` object contains all attributes of markup one except `length`, `offset` and `type` which means that in this example it contains only `uri` parameter.

## Singleton tags:

There is also a way of defining singleton tags, e.g. line breaks - Stylizer will treat as singleton every tag without `end` parameter defined.

It can be defined in an array passed to constructor:
```
    ...
    [
        'beginning' => '<br>',
        'type' => 'br',
    ],
    ...
```
We can define it using `addTag` method as well:
```
$stylizer->addTag('br', '<br>');
```

If we define `br` tag, Stylizer will also add line break in every place in text before `PHP_EOL` sign occurence.

## Tests:

This library contains unit tests and some other CI tools which can be easily run using Node.js. To do it install Node.js environment, then Grunt CLI:
```
sudo npm install -g grunt-cli
```
and project dependencies:
```
npm install
```
To start testing just simply execute:
```
grunt test
```
