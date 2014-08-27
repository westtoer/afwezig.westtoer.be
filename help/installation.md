Installation
============
1. Clone the git directory onto your server
2. Look for the MySQL-dump that is present. Import it into a database.
and you'll see your UiTID in an XML-format under rdf:id.
3. Go to app/Config/database.php and change the credentials to match to your database's.
4. Go to app/Config/core.php and change line 225 and line 230 to a random string matching the format. More on that can be found on the CakePHP documentation.
5. Go to app/Config/UiTID.php or mv UiTID.php.default to UiTID.php. Change the test keys to your production keys.
6. Go to app/Config/Administrator.php and configurate main email, fallback base dir (neccesary for email) and export dir (full path e.g. /var/www/html/afwezig/exports).
7. You now should be able to run Afwezig.

Whoa?! What about my Administrator account?
===========================================
The first time you will log on, Afwezig will detect that there isn't a Administrator. It then will create a User and Employee account, and bind them to the first UiTID that connects with it.

Troubleshooting
===============
- If you have problems, make sure that your directories (especialy the tmps of cake) have sufficient right access. You can test this by chmodding your
complete dir to 777.
- If you are not able to login with UiTID, make sure the production server is set to www.uitid.be/uitid/rest and not uitid.be/uitid/rest. This setting can be
found in the app/Config/UiTID.php file
