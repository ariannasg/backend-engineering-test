"App Analyse Metrics" 
=================================================

Backend Engineering Test
--------------
Write a command that generates a metrics summary from a given file. 
Include some basic statistics: average, min, max, median; and if there were any under-performing periods of download.

Comments and assumptions
--------------
These are some of the assumptions when implementing the solution:
- Extension of file is only json for now
- All metrics in the file have the same unit: bytes
- The expected stats metrics will have the same unit: megabits
- All metrics in the file contain sorted dates from oldest to newest
- All files contain successful requests with code ok; otherwise would have to  those checks
- The under-performing periods will be represented by a start and an end date

A list of TODOs was added to the [TODO file](TODO.md) in the project.

How to install it?
--------------
Make sure you set your env variables correctly in a .env file, especially including **SUMMARY_OWNER** and 
**SUMMARY_VERSION**. You can find example of their values defined in the [phpunit.xml.dist file](phpunit.xml.dist)

Run the following commands from the project root:

`
docker-compose build
`

`
make resolve-dependencies
`

Make sure it worked by running the tests or one of the commands on the Makefile. i.e

`
make test
`

`
make analyse-file-one
`

`
make analyse-file-one-in-bytes
`

How to access the container?
--------------
Run the command:
`
make shell
`
 
How to run the tests?
--------------
We'll use only [PHPUnit](https://phpunit.de/) for testing. You can find the classes under [./tests](tests).

For running all tests: 
`make test`