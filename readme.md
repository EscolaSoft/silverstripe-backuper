# SilverStripe backup module (assets and database)

It creates zip backup of assets folder and sql backup and allows to download them.
Provides ability to  to restore sql backup from sql file.

*Warning* at this stage this module is _extremly insceure_ and should not be installed on live server.

## Requirements
 * SilverStripe ^3.1
 * ifsnop/mysqldump-php
 * alchemy/zippy
 * aura/sql
## Installation


```
composer require qunabu/silverstripe-backuper
```

## License
See [License](license.md)

## Documentation

## Maintainers
 * Mateusz Wojczal <mateusz@qunabu.comm>
 
## Bugtracker
Bugs are tracked in the issues section of this repository. Before submitting an issue please read over 
existing issues to ensure yours is unique. 
 
If the issue does look like a new bug:
 
 - Create a new issue
 - Describe the steps required to reproduce your issue, and the expected outcome. Unit tests, screenshots 
 and screencasts can help here.
 - Describe your environment as detailed as possible: SilverStripe version, Browser, PHP version, 
 Operating System, any installed SilverStripe modules.
 
Please report security issues to the module maintainers directly. Please don't file security issues in the bugtracker.
 
## Development and contribution
If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.
