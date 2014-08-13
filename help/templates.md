Templates
=========
If you want to edit the templates used in Afwezig, you'll need to edit the template files or the views
depending on what you are trying to do.

To change the way the data is displayed in the interface, you'll need to edit the views. These can be
found in the app/Views directory. On how to edit views, read further down.

Templates are stored there as well, but in the folder app/Views/layouts. There are two layouts being used
in Afwezig. One for external viewing (the login.ctp found as app/Views/layouts/login.ctp), which handles
registration requests, login errors and the login itself.

The internal view is the default CakePHP view (found as app/Views/layouts/default.ctp). Many things are done
here. For example, an element (a small puzzlepiece of a page abstracted away in app/Views/Elements) has been
called to display the [UniBar](http://intranet.westtoer.be/?q=node/99). This call is being done with the
`$this->element` function.

The rest of the default page is pretty straight forward.

Views
=====
Views are part of the MVC model. If you are editing these pages, because you want to rearrange how a page looks, its
smart to look at the Controller method same name as the page. The right Controller can be found by looking at the folder
name where a View is stored. E.g. if a View is stored in Employees/index, go to app/Controller/EmployeeController.php and
look for the method index().