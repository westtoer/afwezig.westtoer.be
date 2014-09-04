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
generated with the CakePdf library (found in the /app/Plugin/CakePdf). For more on CakePdf, you can view the [Github repo](https://github.com/ceeram/CakePdf).

##Ambiguous usage of Request
In the code, you'll see the usage of a Model Request which we created and an object request introduced by CakePHP. Mind the capitalisation. Our
version of Request is used to manage actual requests where a users asks permission to his supervisor. CakePHP uses request to access GET and POST
variables. It also has a few function to check which type of request is sent ($this->request->is('post') checks if the current request is a post request).
[For more on CakePHP's request handling](http://book.cakephp.org/2.0/en/controllers/request-response.html).

##Mixed usage of internal_id and id
The Employee record has an internal_id and an id column. Both are used for referencing records in the database. This can be weird, as some of the joins happen on employee_id of a
different table, but actually hold the internal_id.

Here's a list of where internal_id is used in the database (Model.Join):
- Employee.Supervisor
- Request.Replacement
- Stream.Employee

#CakePHP and saving data
You'll see throughout the code that the following code is being used
```
$this->ModelName->save();
```
You'll notice that the param the save method uses, is almost always a nested array with the name of the Model

```
$saveThis = array('ModelName' => array('field1' => 'value1');
```

This however, is not possible when your model does not join. If this is the case, you don't have to create a nested array:
```
$saveThis = array('field1' => 'value1');
```
