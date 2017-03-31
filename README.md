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

### Allow some tags and attributes

``` php
// create cleaner...
$cleaner
    ->addAllowedTags(['p', 'span'])
    ->addAllowedAttributes(['class']);
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

> Note that allowed attributes are not bound to a specific tag. In the example
> above, the href attribute will be valid for every HTML tag.

## Limitations

### Root mixed content
Mixed content is not allowed in root position.

``` html
<!-- not valid -->
some root <strong>mixed</strong> <em>content</em>

<!-- valid -->
<p>some root <strong>mixed</strong> <em>content</em></p>
```

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
