#Exports

##What types of exports are there?

In our root directory, we'll find a folder exports, which has two child folders: Backup and JSON.

###Backup
The Backup folder is used
for the endOfYear wizard. When it's started and when it's completed, Afwezig will create a MySQL dump to ensure that the
database can always revert to the most logical point.

###JSON
The JSON folder will have serialised JSON dumps of all the calendar days associated with an export triggered in the
administration panel. This will allow other to analyse which data is sent to Schaubroeck.