# picios\LineByLineTask

The script allows you to stagger complex tasks on individual lines of the text file

Sample code:

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

