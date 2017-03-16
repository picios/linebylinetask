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

