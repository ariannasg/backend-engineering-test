- [] Add tests for MetricsSummaryService.php
- [] Add PHPStan config and write a command in Makefile for running the checks
- [] Add more exceptions when analysing the content/format of file
- [] Add specific cases for getting content from different file formats/extensions. i.e: metrics from a csv file
- [] Improve all other tests
- [] Add a health check endpoint to DefaultController
- [] Add an endpoint to DefaultController for checking the stats. Maybe create a simple page where we
could upload the document and generate the metrics summary
- [] Add logger config and log exceptions thrown from the command depending on discussed severity
- [] Add a code check when getting the file data in case not all the metrics were successful requests with code=ok