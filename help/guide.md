Some arbitrary choices have been made within this project. These choices are sometimes introduced by the CakePHP-logic,
sometimes by PHP and sometimes by our flow of programming. This file wants to contribute some knowledge on how to read
our code as a non-Cake developer, or as a person not familiar with our way of writing code.

##Getting started with CakePHP
If you want to edit this project, but don't know how CakePHP works, please refer to the [Blog tutorial](http://book.cakephp.org/2.0/en/getting-started.html#blog-tutorial) of CakePHP. This
will give you a basic understanding on how to manipulate the project.

##MVC
CakePHP uses the Model-View-Controller logic to access, edit and display data. In the folder Model, you'll find classes
that correspond with objects (read tables) in the database. For example, the Model Request.php will describe the
records in the table requests.

If you keep in mind the naming conventions of CakePHP, this will allow us to access the data in the controller RequestsController.php
by writing "$this->Request->find('all')".

In the controller we'll manipulate/analyse the data, create some new records or send mails. We then can give the data to
the views by setting the data. We do this by writing "$this->set('variable_name', data)" in the controller. If we are in
the right View (in this case /View/Requests/index.ctp), we can access this data by just calling $variable_name.

CakePHP has an arbitrary extension for it's view files, so when creating a new view, don't save with .html or .php, but
with .ctp.

[For more about CakePHP's MVC](http://book.cakephp.org/2.0/en/cakephp-overview/understanding-model-view-controller.html)

##Folder structure
The folder structure is pretty straightforward. The app directory holds all the code written on top of the framework. To access
html dependancies like CSS or Javascript files, you can navigate to the app/webroot folder. This is used by URL rewrite to
have easy access to them for Views. The dependancies are called in the layout. These layouts can be defined by actions in
the controller, but if empty, the default.ctp is used (found in app/View/Layouts).

##SQL Query access
We have introduced a debug feature, that only administrators can view. If you append a get variable in the url sql=1, you can
see which calls CakePHP sends to the MySQL server.

##PDF
Some pages can be viewed as PDF. These however, use different views than the normal webpages. They have a different layout, found in
(app/View/Layouts/pdf/default.ctp) and will have different Views depending on the controller, e.g. (app/View/Admin/pdf). Our PDF's are
generated with the CakePdf library (found in the /app/Plugin/CakePdf). For more on CakePdf, you can view the [Github repo](https://github.com/ceeram/CakePdf)