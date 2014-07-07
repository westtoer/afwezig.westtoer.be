Westtoer Op Verlof
==================

Westtoer Op Verlof (name pending) is an application created for Westtoer - Tourism West-Flanders to manage their vacation days.
This project is currently in active development, but is in it's early stages. The code has not been optimized, or even cleaned
up! So use at own risk.


CakePHP
-------

We are using CakePHP to create this application. If you would like to spin this application yourself, feel free to
contact me at im@nielsvermaut.eu and I will guide you through the process.

UiTID in CakePHP
----------------

I am going to create a CakePHP plugin for UiTID OAuth, but if you would like to check out how I did it, you need to copy the
vendors folder into your project and you need to copy the app/Controller/UsersController.php. These will help you implement
login by UiTID.


Registration flow
-----------------

Westtoer wanted a different approach to user registration. The UiTID will be captured when logging in for the first time,
then the user will be presented a list where a Employee-record will already be made. When they having chosen an account,
a supervisor will be asked to verify.

I understand that if you want to deploy this application, that's kind of against your workflow, as this is a highly
situational setup. If you are a little bit capable of working with PHP, just read the CakePHP user tutorial on Authorisation.

If you are not very code-savvy, I'll be creating a non-UiTID version, if Westtoer doesn't mind.


Deployment Steps
----------------

1. Find a copy of the database: Find an empty copy of the database. These will be flying around in the SQL folder, as long
as this system is not complete. However, if you are not able to find one there, you'll best mail me.
2. Copy this folder to somewhere on your webserver. Don't worry about links, as these are all relative.
3. Import the MySQL database on your MySQL server.
4. Edit the app/config/core.php file and edit the hashes to random strings/cypherseeds.
5. Edit the app/config/database.php to correspond with you MySQL settings.
6. Add a MySQL record in the table Employees, be sure to make his Role ID = 1
7. Login with your UiTID and link your account with the Employee-record you've just made.
8. You have succesfully installed Op Verlof. Have a beer or a coffee depending on the hour the day to celebrate!


