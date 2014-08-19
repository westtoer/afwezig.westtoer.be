afwezig.westtoer.be
===================

An Absence Management Tool written for Westtoer

What is this?
=============

Westtoer Afwezig is a Absence management tool, comparable with SAP but much smaller in scope. This tool - written in CakePHP - allows employees to ask permission for an absence, will then ask their supervisor if they can leave and will then generate reports for management.

This is a fairly tailored application, meaning that not many this are configurable. The whole application has been written in Dutch, without implementing i18 support. It's user management is also highly custom, requiring the UiTID-service to login with OAuth.

Why is this public then?
========================

The developers/managers at Westtoer believe in an Open Source space where it is possible. Nothing in this code is business-critical and we hope that people could learn a thing or two (like how to implement UiTID in CakePHP) from this code. 

It will also be able for people to critique the code, something that is neccesary if you want to most performant code. This project has been written by one person, and therefore bugs and a little bit of untidy code is possible.

How do I run it?
================

Installation is pretty straightforward. You can just download this, import the SQL database found in /help/install.sql, follow the steps in /help/installation.md and you'll be spinning in under 10 minutes. 

Keep in mind you'll need an UiTID to login! You can patch this out fairly easily, by removing the UiTID and callback actions, among others, and implementing the vanilla CakePHP UsersController, and you should be able to run this without the need of an external UiTID. However, the spec demanded UiTID so I created it in this manner.

Can we use this?
================

We encourage the use of this code, and therefore we are releasing the code under the [CC-BY-SA 4.0 license](https://creativecommons.org/licenses/by-sa/4.0/). 


How can I contact you?
======================

- For inqueries about new project: im@nielsvermaut.eu
- For questions about this project: marc.portier@westtoer.be

