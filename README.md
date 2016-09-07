# rp_tracker
RP_tracker is a site that aims to make the managing of RP's easier.
This is done in a couple of ways.
The first being is that it stores all the relevant information in one place. No more seperate text files for character sheets and all the modifiers on them in a spreadsheet.
The second way it makes it easier is that the dice-roll tools on this site allow easy access to the total stats a character has. Need to roll someones armour against someones strength? simply select the 2 characters and the stats and click roll. (not yet implemented)

However, RP_tracker is far from finished, and as such it is currently not yet hosted. 
Want to help out but you are unable to write code for the project? Ideas are always welcome, simply write your ideas as issues on the issue tracker.

If you want to see it running all you need is a apache, php and mysql. By default RP_Tracker connects to the database using the username "rp_tracker" and without a password. This can be changed in /application/config/database.php
The database is provided as an sql file located in /database 
this automatically creates an user called "root" with the password "root" which you can use to login to RP_tracker.

The required programs can be installed on windows using wamp (http://www.wampserver.com/en/) and on mac by using mamp(https://www.mamp.info/en/)
Note that when using mamp it is recomended to change the used ports apache and mysql use to their normal defaults rather then the defaults set by mamp. 
This is explained here: https://www.mamp.info/en/documentation/
