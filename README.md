# Line By Line Task

The class allows to stagger complex tasks processed on individual lines of the text file

## Sample code

``` php
require_once 'LineByLineTask.php';
$task = new Picios\Lib\LineByLineTask('bigfile.csv', function($line, $op) {
	echo "{$line}<br />";
});

$task->setLimit(4)->run();
```

Then refresh your script in a browser. Each time you will get a new portion of lines limited by setLimit() method

``` php
$task->setLimit(4);
```

The class requires a writtable directory to save a temporary file. When the filename is not passed, it' tries to create the default one based on input file name in /tmp/ directory.

## Testing

To test the class, run:

```
phpunit test
```

## Homepage

You can read more at [Picios.pl](http://picios.pl/line-line-heavy-task/)