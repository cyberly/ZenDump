# ZenDump
look on my works, ye mighty, and despair

### Requirements
PHP, MySQL, the things they need to talk and a deathwish. zts-php utilities will
also beed needed because why wouldn't this use multithreading? Use the webtatic
repo for ease.

### Install
```
git clone git@git.liquidweb.com:cbyerly/ZenDump.git
cd ZenDump
composter Install
```
Make sure you copy .env.example to .env and fill in the blanks.

### General Workflow
The typical task order for building a fresh data set:
-Build closed ticket list up to a point in time (a day or two back)
-Build active ticket list
-Build ticket data
-Build agent/groups data
-Build macros/triggers/targets/automations
-Build helpcenter articles
-Retrieve HC/ticket attachments
```
scripts/reset_db.sh
zts-php buildClosedList.php
zts-php buildActiveList.php
```
Make necessary edits to line 16 of getData.php to select which ticket list.
```
zts-php getData
```
And so on.

### Notes
-Ticket data for closed list and active list should not go in the same database.
-Incremental information for active list (including attachments) is likely a good
idea to reduce go live window.
