# linebylinetask
The script allows you to stagger complex tasks on individual lines of the text file

Example:
    ``` php
	require_once 'LineByLineTask.php';
    $task = new LineByLineTask('bigfile.csv', function($line, $op) {
		echo "{$line}<br />";
	});

	$task->setLimit(4)->run();
    ```

