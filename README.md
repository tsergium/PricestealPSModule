## "Price steal" module
=========================================

## General
The exercise represents a Prestashop module implementation. The scope of this exercise is too see 
your capability to read and understand a basic Prestashop documentation and to do the development 
required below.

## Short description
We would like to create a module that is able to "steal" product prices from competitor web shops and 
to show to the users/customers some attractive information related to the pricing.

## Description
On the Back End side you need to define a "Price template" area in which the administrator can 
define regular expressions that identifies the price position on the competitor websites; so, each “price 
template” will have 2 elements:
- name of the template (ex: emag.ro)
- input text field in which the admin can define a regular expression (or something else) that 
can tell to the system/logic how to identify the price on that specific competitor page

You need also to create 2 such regular expressions for 2 websites that you choose.

On the product detail level (where the administrator can edit products in the Back End panel) the 
administrator can add competitor links for each products; so in each product detail there will be as 
many “competitor input fields” as many templates where defined  and in there competitor url’s can 
be inserted.

On the Front End side, in the product detail page (for each product) you will show:
- a text: "We have the best price on the market. The prices of our competitors are ...." 
- the list of url’s to the competitors
- for each url we will have a button “See the competitor price” and when this button is clicked a 
read of the competitor website is performed and the price is taken based on the added 
regular expression and the price is shown to the user near the url/link; this action we prefer to 
be done via Ajax.

Info: All these details are shown ONLY for the products that contains such competitor url’s.
