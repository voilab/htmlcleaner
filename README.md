# Voilab HTML cleaner

A HTML cleaner based on SimpleXML, fast and customizable

## Install

Via Composer

Create a composer.json file in your project root:
``` json
{
    "require": {
        "voilab/htmlcleaner": "0.*"
    }
}
```

``` bash
$ composer require voilab/htmlcleaner
```

## Sample dataset

``` html
<p>
    Some paragraph with <strong>bold</strong> or
    <em><u><i>nested tags</i></u></em>.
</p>
<p>
    And a second paragraph (so two roots elements, here) with
    <a href="somesite.org">a cool link</a>,
    <a href="javascript:alert('BAM!');">a bad link</a>
    and some <span class="red">nice attributes to try to keep</span>.
</p>
```

## Basic usage

### All tags stripped

``` php
use \voilab\cleaner\HtmlCleaner;

$cleaner = new HtmlCleaner();
$raw_html = '...'; // take sample dataset above

echo $cleaner->clean($raw_html);
```

### Allow some tags

``` php
// create cleaner...
$cleaner->addAllowedTags(['p', 'strong']);
// call clean method
```

### Allow some tags and attributes (regardless of tags)

``` php
// create cleaner...
$cleaner
    ->addAllowedTags(['p', 'span'])
    ->addAllowedAttributes(['class']);
// call clean method
```

### Allow some attributes only on certain tags

``` php
// create cleaner...
$cleaner
    ->addAllowedTags(['p', 'span'])
    ->addAllowedAttributes([
        // keep attribute "class" only for spans
        new \voilab\cleaner\attribute\Keep('class', 'span'),

        // you can use this shorthand too, as a string
        'style:span'
    ]);
// call clean method
```

## Advanced usage

### Processors

Processors are used to prepare HTML string before it is inserted into a new
SimpleXMLElement (base of the process). They are also used to format the HTML
after it is cleaned. It's some sort of pre-process and post-process.

> The pre-process **must** remove not allowed tags.

#### Standard processor

The standard processor uses `strip_tags()` to remove not allowed tags. After
process, the processor removes all carriage returns from the string.

#### Custom processor

You can create your own processor by implementing
`\voilab\cleaner\processor\Processor`. Do not forget that the pre-process
is responsible of removing all not allowed tags.

### Attributes

Attributes classes are used to validate attributes and their content. By default
an allowed attribute becomes a `\voilab\cleaner\attribute\Keep`. Every
"not allowed" attribute becomes a `\voilab\cleaner\attribute\Remove`.

These two attribute types don't need to be instanciated by you. All attributes
provided as a string in `setAllowedTags()` are converted in `Keep` class.

#### Js attribute

You may want to keep some attributes but check the content. It's true for the
`href` attribute. It can contain a valid URL or some javascript injection.
There is an attribute validator already created for that:

``` php
$cleaner
    ->addAllowedTags(['a'])
    ->addAllowedAttributes([
        new \voilab\cleaner\attribute\Js('href')
    ]);
```

> Note that allowed attributes can be bound or not to a specific tag. In the
> example above, the href attribute will be valid for every HTML tag. If you
> want to bind the attribute to a tag, you need to specify it as a second
> parameter.

## Known limitations

### Root mixed content
Mixed content outside tags is not allowed in root position.

``` html
<!-- not valid: parts "some root " and " special " will disappear -->
some root <strong>mixed</strong> special <em>content</em>

<!-- valid -->
<p>some root <strong>mixed</strong> special <em>content</em></p>
<!-- also valid -->
<p>some root element</p>
<p>and an other root element</p>
```

### Bad HTML format with Standard processor
If HTML is not well formatted, the cleaner will throw an `\Exception`. The
string needs to be perfectly written, because it is processed by
`simplexml_load_string($html)`, which is very strict:

- tags must be closed (`<p></p>` or `<br />`)
- attributes must be wrapped in (double-)quotes (`<hr class="test" />`)
- (double-)quote is not allowed in attribute content, it must be converted in
`&quot;` before `HtmlCleaner::clean()` is called
- opening tag `<` and `&` are not allowed in content, they must be converted
respectivly in `&lt;` and `&amp;` before `HtmlCleaner::clean()` is called

These limitations will eventually be addressed in future releases.

## Testing

``` bash
$ vendor/bin/phpunit --bootstrap vendor/autoload.php tests/
```

## Security

If you discover any security related issues, please use the issue tracker.

## Credits

- [tafel](https://github.com/tafel)
- [voilab](https://github.com/voilab)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
